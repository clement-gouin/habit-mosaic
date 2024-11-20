<template>
    <div>
        <dropdown-input
            v-model="tracker"
            ref="input"
            :id="id"
            :name="name"
            :label="label"
            :placeholder="placeholder ?? 'Select a tracker...'"
            :disabled="disabled"
            :required="required"
            :readonly="readonly"
            :help-text="helpText"
            :color="color"
            :error="error"
            :options="options"
            @search="onSearch"
            :loading="isLoading"
            :debounce="500"
            :no-clear="noClear"
        >
            <template #left>
                <slot name="left"></slot>
            </template>
            <template #item="{value}">
                <tracker-label :tracker="value" />
            </template>
            <template #right>
                <div v-if="withCreateButton" class="cursor-pointer" @click="onCreate" title="New tracker">
                    <i class="fa-solid fa-circle-plus"/>
                </div>
                <slot name="right"></slot>
            </template>
        </dropdown-input>
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
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Tracker, Option, BaseFormInput } from '@interfaces';
import Modal from '@tools/modals/Modal.vue';
import DropdownInput from '@tools/forms/DropdownInput.vue';
import { useAsyncState } from '@composables/useAsyncState';
import TrackerLabel from './TrackerLabel.vue';
import TrackerForm from './TrackerForm.vue';
import { listTrackers } from '@requests/trackers';

interface Props extends BaseFormInput {
    withCreateButton?: boolean,
    noClear?: boolean
}

const props = defineProps<Props>();

const emit = defineEmits(['change']);

const search = ref<string>('');

const trackerModel = defineModel<Tracker|null>();

const { state: options, isReady, isLoading, updateState: updateOptions } = useAsyncState<Option<Tracker>[]>(loadOptions, [], trackerModel.value?.id === undefined);
const disabled = computed<boolean>(() => props.disabled || !isReady.value);
const input = ref<InstanceType<typeof DropdownInput>|null>(null);

const createModal = ref<InstanceType<typeof Modal> | null>(null);

const createForm = ref<InstanceType<typeof TrackerForm>|null>(null);

function trackerToOption (tracker: Tracker): Option<Tracker> {
    return { key: tracker.id.toString(), label: tracker.name, value: tracker };
}

const tracker = computed<Option<Tracker>|null>({
    get (): Option<Tracker> | null {
        return trackerModel.value ? trackerToOption(trackerModel.value) : null;
    },
    set (selected: Option<Tracker>|null) {
        trackerModel.value = (selected?.value as Tracker|undefined) ?? null;
        emit('change', trackerModel.value);
    }
});

function sortTrackers (data: Tracker[]) {
    return data
        .sort((a, b) => a.category.order === b.category.order ? b.order - a.order : b.category.order - a.category.order)
        .reverse();
}

async function loadOptions (): Promise<Option<Tracker>[]> {
    return await listTrackers()
        .then(data => {
            return sortTrackers(data)
                .filter(tracker => search.value.length === 0 || tracker.name.includes(search.value))
                .map(trackerToOption);
        });
}

async function onSearch (value = '') {
    search.value = value;
    await updateOptions();
}

function onCreate () {
    if (disabled.value || props.readonly) {
        return;
    }

    createModal.value.open();
}

function createModalSubmit () {
    createForm.value?.submit()
        .then(async (tracker: Tracker) => {
            createModal.value?.close();
            createForm.value?.reset();
            await updateOptions();
            trackerModel.value = tracker;
            emit('change', trackerModel.value);
        })
        .catch(() => {
            // ignore
        });
}
</script>
