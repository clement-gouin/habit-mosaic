<template>
    <div>
        <dropdown-input
            ref="input"
            :name="name"
            v-model="category"
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
                <category-label :category="value" />
            </template>
            <template v-if="withCreateButton || $slots.addon" #addon>
                <div class="input-group-addon btn btn-primary" @click="onCreate" title="New category"><i class="fa-solid fa-circle-plus"/></div>
                <slot name="addon"/>
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
            <category-form
                ref="createForm"
            />
        </modal>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Category, Option } from '@interfaces';
import Modal from '@tools/Modal.vue';
import DropdownInput from '@tools/forms/DropdownInput.vue';
import { useAsyncState } from '@composables/useAsyncState';
import CategoryForm from './CategoryForm.vue';
import { listCategories } from '@requests/categories';
import CategoryLabel from './CategoryLabel.vue';

interface Props {
    name: string,
    modelValue: Category|null,
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

const createForm = ref<InstanceType<typeof CategoryForm>|null>(null);

function categoryToOption (category: Category): Option {
    return { key: category.id, label: category.name, value: category } as Option;
}

const category = computed<Option|null>({
    get (): Option | null {
        return props.modelValue == null ? null : categoryToOption(props.modelValue);
    },
    set (selected: Option|null) {
        emit('update:modelValue', selected?.value);
    }
});

async function loadOptions (): Promise<Option[]> {
    return await listCategories()
        .then(data => {
            return data
                .filter(category => search.value.length === 0 || category.name.includes(search.value))
                .map(categoryToOption);
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
        .then(async (category: Category) => {
            createModal.value?.close();
            createForm.value?.reset();
            await updateOptions();
            emit('update:modelValue', category);
        })
        .catch(() => {
            // ignore
        });
}
</script>
