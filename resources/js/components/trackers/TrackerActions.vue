<template>
    <div class="join" role="toolbar">
        <button type="button" class="join-item btn btn-sm" :disabled="first" @click="$emit('moveUp')"><i class="fa-solid fa-arrow-up" /></button>
        <button type="button" class="join-item btn btn-sm" :disabled="last" @click="$emit('moveDown')"><i class="fa-solid fa-arrow-down" /></button>
        <button type="button" class="join-item btn btn-sm" @click="updateModal.open()"><i class="fa-solid fa-pen" /></button>
        <button type="button" class="join-item btn btn-sm" @click="onDeleteClick"><i class="fa-solid fa-trash" /></button>
    </div>
    <modal
        ref="updateModal"
        title="Update tracker"
        action-text="Update"
        close-text="Cancel"
        :auto-close="false"
        @close="updateForm.reset()"
        @submit="updateModalSubmit"
    >
        <tracker-form
            ref="updateForm"
            v-model="tracker"
        />
    </modal>
    <confirm-dialog
        ref="confirmDeleteDialog"
        :title="`Delete tracker '${tracker.name}' ?`"
        confirm-button-text="Delete"
        confirm-buttom-color="danger"
        cancel-button-text="Cancel"
    />
</template>

<script setup lang="ts">

import Modal from '@tools/Modal.vue';
import { Tracker } from '@interfaces';
import { watch, ref } from 'vue';
import ConfirmDialog from '@tools/dialogs/ConfirmDialog.vue';
import TrackerForm from './TrackerForm.vue';
import { deleteTracker } from '@requests/trackers';

interface Props {
    modelValue: Tracker
    first?: boolean
    last?: boolean
}

const props = defineProps<Props>();

const tracker = ref<Tracker>(props.modelValue);

const emit = defineEmits(['update:modelValue', 'updated', 'moveUp', 'moveDown']);

const updateModal = ref<InstanceType<typeof Modal> | null>(null);
const updateForm = ref<InstanceType<typeof TrackerForm>|null>(null);
const confirmDeleteDialog = ref<InstanceType<typeof ConfirmDialog>|null>(null);

function updateModalSubmit () {
    updateForm.value?.submit()
        .then(tracker => {
            updateModal.value?.close();
            updateForm.value?.reset();
            emit('update:modelValue', tracker);
            emit('updated');
        })
        .catch(() => {
            // ignore
        });
}

async function onDeleteClick () {
    if (await confirmDeleteDialog.value.show()) {
        deleteTracker(tracker.value)
            .then(() => {
                emit('updated');
            });
    }
}

watch(() => props.modelValue, newValue => {
    tracker.value = newValue;
});
</script>
