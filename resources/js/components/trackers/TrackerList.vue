<template>
    <datatable :total="trackers.length" :data="trackers" :columns="columns" :with-pagination="false" :loading="loading" stripped>
        <template #col-name="{row}">
            <tracker-label :tracker="row" />
        </template>
        <template #col-category="{row}">
            <category-label :category="row.category" />
        </template>
        <template #col-target_score="{value}">
            {{ value.toFixed(1) }}
        </template>
        <template #col-value_step="{value}">
            {{ value.toFixed(1) }}
        </template>
        <template #col-target_value="{value}">
            {{ value.toFixed(1) }}
        </template>
        <template #col-actions="{index}">
            <tracker-actions
                v-model="trackers[index]"
                @updated="fetchData"
                @move-up="() => swapOrder(trackers[index], trackers[index - 1])"
                @move-down="() => swapOrder(trackers[index], trackers[index + 1])"
                :first="index === 0 || trackers[index - 1].category.id !== trackers[index].category.id"
                :last="index === trackers.length - 1 || trackers[index + 1].category.id !== trackers[index].category.id"
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
import TrackerActions from './TrackerActions.vue';
import CategoryLabel from '@components/categories/CategoryLabel.vue';
import TrackerLabel from './TrackerLabel.vue';
import useIdleWatcher from '@composables/useIdleWatcher';

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
        label: 'Target score',
        cssClass: 'd-none d-xl-table-cell'
    },
    {
        id: 'value_step',
        label: 'Value step',
        cssClass: 'd-none d-xl-table-cell'
    },
    {
        id: 'target_value',
        label: 'Target value',
        cssClass: 'd-none d-xl-table-cell'
    },
    {
        id: 'unit',
        label: 'Unit',
        cssClass: 'd-none d-md-table-cell'
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
        .sort((a, b) => a.category.order === b.category.order ? b.order - a.order : b.category.order - a.category.order)
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
            fetchData();
        })
        .catch(() => {
            // ignore
        });
}

useIdleWatcher(fetchData);
</script>
