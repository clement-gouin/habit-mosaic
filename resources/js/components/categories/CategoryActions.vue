<template>
    <div class="join" role="toolbar">
        <button type="button" class="join-item btn btn-sm" :disabled="first" @click="$emit('moveUp')"><i class="fa-solid fa-arrow-up" /></button>
        <button type="button" class="join-item btn btn-sm" :disabled="last" @click="$emit('moveDown')"><i class="fa-solid fa-arrow-down" /></button>
        <button type="button" class="join-item btn btn-sm" @click="updateModal.open()"><i class="fa-solid fa-pen" /></button>
        <button type="button" class="join-item btn btn-sm" @click="onDeleteClick"><i class="fa-solid fa-trash" /></button>
    </div>
    <modal
        ref="updateModal"
        title="Update category"
        action-text="Update"
        close-text="Cancel"
        :auto-close="false"
        @close="updateForm.reset()"
        @submit="updateModalSubmit"
    >
        <category-form
            ref="updateForm"
            v-model="category"
        />
    </modal>
    <confirm-dialog
        ref="confirmDeleteDialog"
        :title="`Delete category '${category.name}' ?`"
        confirm-button-text="Delete"
        confirm-buttom-color="danger"
        cancel-button-text="Cancel"
    />
</template>

<script setup lang="ts">

import CategoryForm from './CategoryForm.vue';
import Modal from '@tools/Modal.vue';
import { Category } from '@interfaces';
import { watch, ref } from 'vue';
import { deleteCategory } from '@requests/categories';
import ConfirmDialog from '@tools/dialogs/ConfirmDialog.vue';

interface Props {
    modelValue: Category
    first?: boolean
    last?: boolean
}

const props = defineProps<Props>();

const category = ref<Category>(props.modelValue);

const emit = defineEmits(['update:modelValue', 'updated', 'moveUp', 'moveDown']);

const updateModal = ref<InstanceType<typeof Modal> | null>(null);
const updateForm = ref<InstanceType<typeof CategoryForm>|null>(null);
const confirmDeleteDialog = ref<InstanceType<typeof ConfirmDialog>|null>(null);

function updateModalSubmit () {
    updateForm.value?.submit()
        .then(category => {
            updateModal.value?.close();
            updateForm.value?.reset();
            emit('update:modelValue', category);
            emit('updated');
        })
        .catch(() => {
            // ignore
        });
}

async function onDeleteClick () {
    if (await confirmDeleteDialog.value.show()) {
        deleteCategory(category.value)
            .then(() => {
                emit('updated');
            });
    }
}

watch(() => props.modelValue, newValue => {
    category.value = newValue;
});
</script>
