<template>
    <div class="btn-toolbar" role="toolbar">
        <button type="button" class="btn btn-sm btn-outline-primary me-2" @click="updateModal.open()"><i class="fa-solid fa-pen" /></button>
        <button type="button" class="btn btn-sm btn-outline-danger" @click="onDeleteClick"><i class="fa-solid fa-trash" /></button>
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
        :title="`Delete category '${modelValue.name}' ?`"
        confirm-button-text="Delete"
        confirm-buttom-color="danger"
        cancel-button-text="Cancel"
    />
</template>

<script setup lang="ts">

import CategoryForm from './CategoryForm.vue';
import Modal from '@tools/Modal.vue';
import { Category } from '@interfaces';
import { createAlert } from '@utils/alerts';
import { watch, ref } from 'vue';
import { deleteCategory } from '@requests/categories';
import ConfirmDialog from '@tools/dialogs/ConfirmDialog.vue';

interface Props {
    modelValue: Category
}

const props = defineProps<Props>();

const category = ref<Category>(props.modelValue);

const emit = defineEmits(['update:modelValue', 'updated']);

const updateModal = ref<InstanceType<typeof Modal> | null>(null);
const updateForm = ref<InstanceType<typeof CategoryForm>|null>(null);
const confirmDeleteDialog = ref<InstanceType<typeof ConfirmDialog>|null>(null);

function updateModalSubmit () {
    updateForm.value?.submit()
        .then(category => {
            updateModal.value?.close();
            updateForm.value?.reset();
            createAlert('success', 'Category updated');
            emit('update:modelValue', category);
            emit('updated');
        })
        .catch(() => {
            // ignore
        });
}

async function onDeleteClick () {
    if (await confirmDeleteDialog.value.show()) {
        deleteCategory(props.modelValue)
            .then(() => {
                createAlert('success', 'Category deleted');
                emit('updated');
            })
            .catch(() => { createAlert('danger', 'An error has occurred'); });
    }
}

watch(() => props.modelValue, newValue => {
    category.value = newValue;
});
</script>

<style scoped>

</style>
