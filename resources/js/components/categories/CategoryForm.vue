<template>
    <bs-form horizontal>
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
            <text-input
                class="mb-2"
                name="order"
                label="Order"
                v-model="category.order"
                v-model:error="errors['order']"
                type="number"
                required
            />
        </div>
    </bs-form>
</template>

<script setup lang="ts">
import { Category } from '@interfaces';
import { ref, watch } from 'vue';
import { createCategory, updateCategory } from '@requests/categories';
import { handleFormErrors } from '@utils/forms';
import TextInput from '@tools/forms/TextInput.vue';
import IconInput from '@tools/forms/IconInput.vue';
import BsForm from '@tools/forms/BsForm.vue';

interface Props {
    modelValue?: Category
}

const props = defineProps<Props>();

const formInitValues: Category = {
    name: '',
    icon: '',
    order: 0
};

const category = ref<Category>(loadDataFromProps());
const errors = ref<Record<string, string>>({});

function reset () {
    errors.value = {};
    category.value = loadDataFromProps();
}

function loadDataFromProps (): Category {
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

<style scoped>

</style>
