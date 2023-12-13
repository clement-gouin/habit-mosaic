<template>
    <datatable :total="trackers.length" :data="trackers" :columns="columns" :with-pagination="false" :loading="loading">
        <template #col-name="{row}">
            <tracker-label :tracker="row" />
        </template>
        <template #col-category="{row}">
            <template v-if="row.category">
                <category-label :category="row.category" />
            </template>
            <template v-else>(none)</template>
        </template>
        <template #col-target_score="{row}">
            {{ row.target_score.toFixed(1) }}
        </template>
        <template #col-actions="{index}">
            <tracker-actions
                v-model="trackers[index]"
                @updated="fetchData"
                @move-up="() => swapOrder(trackers[index], trackers[index - 1])"
                @move-down="() => swapOrder(trackers[index], trackers[index + 1])"
                :first="index === 0"
                :last="index === trackers.length - 1"
            />
        </template>
    </datatable>
    <div class="d-grid">
        <button type="button" class="btn btn-primary" @click="createModal.open()"><i class="fa-solid fa-circle-plus" /> New tracker</button>
    </div>
    <modal
        ref="createModal"
        title="Create new tracker"
        action-text="Create"
        close-text="Cancel"
        :auto-close="false"
        @close="createForm.reset()"
        @submit="createModalSubmit"
    >
        <tracker-form
            ref="createForm"
        />
    </modal>
</template>

<script setup lang="ts">
import Datatable from '@tools/tables/Datatable.vue';
import { TableColumn, Tracker } from '@interfaces';
import { ref } from 'vue';
import { listTrackers, updateTracker } from '@requests/trackers';
import Modal from '@tools/Modal.vue';
import TrackerForm from './TrackerForm.vue';
import { createAlert } from '@utils/alerts';
import TrackerActions from './TrackerActions.vue';
import CategoryLabel from '../categories/CategoryLabel.vue';
import TrackerLabel from './TrackerLabel.vue';

interface Props {
    modelValue: Tracker[],
}

const props = defineProps<Props>();

const trackers = ref<Tracker[]>(sortTrackers(props.modelValue));
const createModal = ref<InstanceType<typeof Modal> | null>(null);
const createForm = ref<InstanceType<typeof TrackerForm>|null>(null);
const loading = ref<boolean>(false);

const columns: TableColumn[] = [
    {
        id: 'name',
        label: 'Name'
    },
    {
        id: 'category',
        label: 'Category'
    },
    {
        id: 'target_score',
        label: 'Target score'
    },
    {
        id: 'actions',
        label: '',
        cssClass: 'text-left',
        cssStyle: 'width: 11em; vertical-align: middle'
    }
];

function fetchData () {
    loading.value = true;
    listTrackers()
        .then(data => {
            trackers.value = sortTrackers(data);
        })
        .finally(() => {
            loading.value = false;
        });
}

function sortTrackers (data: Tracker[]) {
    return data
        .sort((a, b) => (a.category?.order ?? 0) === (b.category?.order ?? 0) ? b.order - a.order : (b.category?.order ?? 0) - (a.category?.order ?? 0))
        .reverse();
}

function swapOrder (tracker1: Tracker, tracker2: Tracker) {
    loading.value = true;
    [tracker1.order, tracker2.order] = [tracker2.order as number, tracker1.order as number];
    Promise.all([updateTracker(tracker1), updateTracker(tracker2)])
        .finally(fetchData);
}

function createModalSubmit () {
    createForm.value?.submit()
        .then(() => {
            createModal.value?.close();
            createForm.value?.reset();
            createAlert('success', 'Tracker created');
            fetchData();
        })
        .catch(() => {
            // ignore
        });
}
</script>

<style scoped>

</style>
