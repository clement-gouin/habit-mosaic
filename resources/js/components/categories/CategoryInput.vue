<template>
    <div>
        <dropdown-input
            v-model="category"
            ref="input"
            :id="id"
            :name="name"
            :label="label"
            :placeholder="placeholder ?? 'Select a category...'"
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
                <category-label :category="value" />
            </template>
            <template #right>
                <div v-if="withCreateButton" class="cursor-pointer" @click="onCreate" title="New category">
                    <i class="fa-solid fa-circle-plus"/>
                </div>
                <slot name="left"></slot>
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
            <category-form
                ref="createForm"
            />
        </modal>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { BaseFormInput, Category, Option } from '@interfaces';
import Modal from '@tools/modals/Modal.vue';
import DropdownInput from '@tools/forms/DropdownInput.vue';
import { useAsyncState } from '@composables/useAsyncState';
import CategoryForm from './CategoryForm.vue';
import { listCategories } from '@requests/categories';
import CategoryLabel from './CategoryLabel.vue';

interface Props extends BaseFormInput {
    withCreateButton?: boolean,
    noClear?: boolean
}

const props = defineProps<Props>();

const emit = defineEmits(['change']);

const search = ref<string>('');

const categoryModel = defineModel<Category|null>();

const { state: options, isReady, isLoading, updateState: updateOptions } = useAsyncState<Option<Category>[]>(loadOptions, [], categoryModel.value?.id === undefined);
const disabled = computed<boolean>(() => props.disabled || !isReady.value);
const input = ref<InstanceType<typeof DropdownInput>|null>(null);

const createModal = ref<InstanceType<typeof Modal> | null>(null);

const createForm = ref<InstanceType<typeof CategoryForm>|null>(null);

function categoryToOption (category: Category): Option<Category> {
    return { key: category.id.toString(), label: category.name, value: category };
}

const category = computed<Option<Category>|null>({
    get (): Option<Category> | null {
        return categoryModel.value ? categoryToOption(categoryModel.value) : null;
    },
    set (selected: Option<Category>|null) {
        categoryModel.value = (selected?.value as Category|undefined) ?? null;
        emit('change', categoryModel.value);
    }
});

function sortCategories (data: Category[]) {
    return data
        .sort((a, b) => b.order - a.order)
        .reverse();
}

async function loadOptions (): Promise<Option<Category>[]> {
    return await listCategories()
        .then(data => {
            return sortCategories(data)
                .filter(category => search.value.length === 0 || category.name.includes(search.value))
                .map(categoryToOption);
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
        .then(async (category: Category) => {
            createModal.value?.close();
            createForm.value?.reset();
            await updateOptions();
            categoryModel.value = category;
            emit('change', categoryModel.value);
        })
        .catch(() => {
            // ignore
        });
}
</script>
