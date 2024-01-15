<template>
    <div>
        <dropdown-input
            ref="input"
            :name="name"
            v-model="tracker"
            :placeholder="placeholder"
            :label="label"
            :error="error"
            :disabled="disabled"
            :required="required"
            :options="options"
            @search="onSearch"
            :help-text="helpText"
            :readonly="readonly"
            :loading="isLoading"
            :debounce="500"
            with-highlight
            :cursor-offset="cursorOffset ?? (withCreateButton ? '3em' : '1em')"
            @change="onChange"
            :notice="notice"
        >
            <template #item="{value}">
                <tracker-label :tracker="value" />
            </template>
            <template v-if="withCreateButton || $slots.addon" #addon>
                <div v-if="withCreateButton" class="input-group-addon btn btn-primary" @click="onCreate" title="New tracker"><i class="fa-solid fa-circle-plus"/></div>
                <slot name="addon"/>
            </template>
            <template v-if="$slots.label" #label>
                <slot name="label"></slot>
            </template>
            <template v-if="$slots.notice" #notice>
                <slot name="notice"></slot>
            </template>
        </dropdown-input>
        <modal
            ref="createModal"
            title="Create new category"
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
import { Tracker, Option } from '@interfaces';
import Modal from '@tools/Modal.vue';
import DropdownInput from '@tools/forms/DropdownInput.vue';
import { useAsyncState } from '@composables/useAsyncState';
import TrackerLabel from './TrackerLabel.vue';
import TrackerForm from './TrackerForm.vue';
import { listTrackers } from '@requests/trackers';

interface Props {
    name: string,
    modelValue: Tracker|null,
    placeholder?: string,
    label?: string,
    helpText?: string,
    error?: string,
    disabled?: boolean,
    required?: boolean,
    readonly?: boolean,
    withCreateButton?: boolean,
    notice?: string,
    cursorOffset?: string
}

const props = defineProps<Props>();
const emit = defineEmits(['update:modelValue', 'change']);

const search = ref<string>('');

const { state: options, isReady, isLoading, updateState: updateOptions } = useAsyncState<Option[]>(loadOptions, [], props.modelValue?.id === undefined);
const disabled = computed<boolean>(() => props.disabled || !isReady.value);
const input = ref<InstanceType<typeof DropdownInput>|null>(null);

const createModal = ref<InstanceType<typeof Modal> | null>(null);

const createForm = ref<InstanceType<typeof TrackerForm>|null>(null);

function trackerToOption (tracker: Tracker): Option {
    return { key: tracker.id, label: tracker.name, value: tracker } as Option;
}

const tracker = computed<Option|null>({
    get (): Option | null {
        return props.modelValue == null ? null : trackerToOption(props.modelValue);
    },
    set (selected: Option|null) {
        emit('update:modelValue', selected?.value);
    }
});

function sortTrackers (data: Tracker[]) {
    return data
        .sort((a, b) => (a.category?.order ?? 0) === (b.category?.order ?? 0) ? b.order - a.order : (b.category?.order ?? 0) - (a.category?.order ?? 0))
        .reverse();
}

async function loadOptions (): Promise<Option[]> {
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

function onChange (value: Option | null) {
    emit('change', value?.value);
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
            emit('update:modelValue', tracker);
        })
        .catch(() => {
            // ignore
        });
}
</script>
