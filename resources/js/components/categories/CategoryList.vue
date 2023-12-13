<template>
    <datatable :total="categories.length" :data="categories" :columns="columns" :with-pagination="false" :loading="loading">
        <template #col-name="{row}">
            <category-label :category="row" />
        </template>
        <template #col-actions="{index}">
            <category-actions
                v-model="categories[index]"
                @updated="fetchData"
                @move-up="() => swapOrder(categories[index], categories[index - 1])"
                @move-down="() => swapOrder(categories[index], categories[index + 1])"
                :first="index === 0"
                :last="index === categories.length - 1"
            />
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
import Datatable from '@tools/tables/Datatable.vue';
import { Category, TableColumn } from '@interfaces';
import { ref } from 'vue';
import Modal from '@tools/Modal.vue';
import CategoryForm from './CategoryForm.vue';
import { createAlert } from '@utils/alerts';
import { listCategories, updateCategory } from '@requests/categories';
import CategoryActions from './CategoryActions.vue';
import CategoryLabel from './CategoryLabel.vue';

interface Props {
    modelValue: Category[],
}

const props = defineProps<Props>();

const categories = ref<Category[]>(props.modelValue);
const createModal = ref<InstanceType<typeof Modal> | null>(null);
const createForm = ref<InstanceType<typeof CategoryForm>|null>(null);
const loading = ref<boolean>(false);

const columns: TableColumn[] = [
    {
        id: 'name',
        label: 'Name'
    },
    {
        id: 'actions',
        label: '',
        cssStyle: 'width: 11em; vertical-align: middle'
    }
];

function fetchData () {
    loading.value = true;
    listCategories()
        .then(data => {
            categories.value = data;
        })
        .finally(() => {
            loading.value = false;
        });
}

function swapOrder (category1: Category, category2: Category) {
    loading.value = true;
    [category1.order, category2.order] = [category2.order as number, category1.order as number];
    Promise.all([updateCategory(category1), updateCategory(category2)])
        .finally(fetchData);
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
