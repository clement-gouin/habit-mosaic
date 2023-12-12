<template>
    <datatable :total="categories.length" :data="categories" :columns="columns" :with-pagination="false">
        <template #col-name="{row}">
            <i class="fa-xs " v-if="row.icon" :class="mapToClassName(row.icon)"></i>
            {{ row.name }}
        </template>
        <template #col-actions="{index}">
            <category-actions v-model="categories[index]" @updated="fetchData" />
        </template>
    </datatable>
    <div class="d-grid">
        <button type="button" class="btn btn-primary" @click="createModal.open()"><i class="fa-solid fa-circle-plus" /> New category</button>
    </div>
    <modal
        ref="createModal"
        title="Create new category"
        action-text="Create"
        close-text="Cancel"
        :auto-close="false"
        @close="createForm.reset()"
        @submit="createModalSubmit"
    >
        <category-form
            ref="createForm"
        />
    </modal>
</template>

<script setup lang="ts">
import { mapToClassName } from '@utils/icons';
import Datatable from '@tools/tables/Datatable.vue';
import { Category, TableColumn } from '@interfaces';
import { ref } from 'vue';
import Modal from '@tools/Modal.vue';
import CategoryForm from './CategoryForm.vue';
import { createAlert } from '@utils/alerts';
import { listCategories } from '@requests/categories';
import CategoryActions from './CategoryActions.vue';

interface Props {
    modelValue: Category[],
}

const props = defineProps<Props>();

const categories = ref<Category[]>(props.modelValue);
const createModal = ref<InstanceType<typeof Modal> | null>(null);
const createForm = ref<InstanceType<typeof CategoryForm>|null>(null);

const columns: TableColumn[] = [
    {
        id: 'name',
        label: 'Name'
    },
    {
        id: 'actions',
        label: '',
        cssClass: 'text-left',
        cssStyle: 'width: 10em; vertical-align: middle'
    }
];

function fetchData () {
    listCategories({})
        .then(data => {
            categories.value = data;
        });
}

function createModalSubmit () {
    createForm.value?.submit()
        .then(() => {
            createModal.value?.close();
            createForm.value?.reset();
            createAlert('success', 'Category created');
            fetchData();
        })
        .catch(() => {
            // ignore
        });
}
</script>

<style scoped>

</style>
