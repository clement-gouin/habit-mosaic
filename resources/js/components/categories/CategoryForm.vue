<template>
    <div>
        <text-input
            class="mb-2"
            name="name"
            label="Name"
            v-model="category.name"
            v-model:error="errors['name']"
            required
        />
        <icon-input
            class="mb-2"
            name="icon"
            label="Icon"
            v-model="category.icon"
            v-model:error="errors['icon']"
        />
    </div>
</template>

<script setup lang="ts">
import { Category, CategoryData } from '@interfaces';
import { ref, watch } from 'vue';
import { createCategory, updateCategory } from '@requests/categories';
import { handleFormErrors } from '@utils/forms';
import TextInput from '@tools/forms/TextInput.vue';
import IconInput from '@tools/forms/IconInput.vue';

interface Props {
    modelValue?: CategoryData
}

const props = defineProps<Props>();

const formInitValues: CategoryData = {
    name: '',
    icon: ''
};

const category = ref<CategoryData>(loadDataFromProps());
const errors = ref<Record<string, string>>({});

function reset () {
    errors.value = {};
    category.value = loadDataFromProps();
}

function loadDataFromProps (): CategoryData {
    if (props.modelValue) {
        return {
            ...formInitValues,
            ...props.modelValue
        };
    }
    return { ...formInitValues };
}

async function submit (): Promise<Category|void> {
    errors.value = {};
    if (category.value.id !== undefined) {
        return updateCategory(category.value)
            .catch(error => {
                errors.value = handleFormErrors(error);
                throw error;
            });
    } else {
        return createCategory(category.value)
            .catch(error => {
                errors.value = handleFormErrors(error);
                throw error;
            });
    }
}

defineExpose({ submit, reset });

watch(() => props.modelValue, loadDataFromProps);
</script>
