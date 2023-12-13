<template>
    <bs-form horizontal>
        <div>
            <category-input
                class="mb-2"
                name="category_id"
                label="Category"
                v-model="tracker.category"
                v-model:error="errors['category_id']"
            />
            <text-input
                class="mb-2"
                name="name"
                label="Name"
                v-model="tracker.name"
                v-model:error="errors['name']"
                required
            />
            <icon-input
                class="mb-2"
                name="icon"
                label="Icon"
                v-model="tracker.icon"
                v-model:error="errors['icon']"
                required
            />
            <checkbox-input
                class="mb-2"
                name="single"
                label="Single step tracker"
                v-model:checked="tracker.single"
                @change="onCheckSingle"
            />
            <template v-if="!tracker.single">
                <text-input
                    class="mb-2"
                    name="unit"
                    label="Unit"
                    v-model="tracker.unit"
                    v-model:error="errors['unit']"
                />
                <text-input
                    class="mb-2"
                    name="value_step"
                    label="Value step"
                    v-model="tracker.value_step"
                    v-model:error="errors['value_step']"
                    type="number"
                    required
                />
                <text-input
                    class="mb-2"
                    name="target_value"
                    label="Target value"
                    v-model="tracker.target_value"
                    v-model:error="errors['target_value']"
                    type="number"
                    required
                />
            </template>
            <text-input
                class="mb-2"
                name="target_score"
                label="Target score"
                v-model="tracker.target_score"
                v-model:error="errors['target_score']"
                type="number"
                required
            />
        </div>
    </bs-form>
</template>

<script setup lang="ts">
import { Base, Tracker } from '@interfaces';
import { ref, watch } from 'vue';
import { handleFormErrors } from '@utils/forms';
import TextInput from '@tools/forms/TextInput.vue';
import IconInput from '@tools/forms/IconInput.vue';
import BsForm from '@tools/forms/BsForm.vue';
import { createTracker, updateTracker } from '@requests/trackers';
import CheckboxInput from '@tools/forms/CheckboxInput.vue';
import CategoryInput from '../categories/CategoryInput.vue';

interface Props {
    modelValue?: Tracker
}

const props = defineProps<Props>();

const singleStepValues: Base = {
    unit: undefined,
    value_step: 1,
    target_value: 1,
    single: true
};

const formInitValues: Tracker = {
    category: undefined,
    name: '',
    icon: '',
    target_score: 1,

    unit: undefined,
    value_step: 1,
    target_value: 1,
    single: true
};

const tracker = ref<Tracker>(loadDataFromProps());
const errors = ref<Record<string, string>>({});

function reset () {
    errors.value = {};
    tracker.value = loadDataFromProps();
}

function loadDataFromProps (): Tracker {
    if (props.modelValue) {
        return {
            ...formInitValues,
            ...props.modelValue
        };
    }
    return { ...formInitValues };
}

async function submit (): Promise<Tracker|void> {
    errors.value = {};
    if (tracker.value.id !== undefined) {
        return updateTracker(tracker.value)
            .catch(error => {
                errors.value = handleFormErrors(error);
                throw error;
            });
    } else {
        return createTracker(tracker.value)
            .catch(error => {
                errors.value = handleFormErrors(error);
                throw error;
            });
    }
}

function onCheckSingle (value: boolean) {
    tracker.value.single = value;
    if (value) {
        tracker.value = {
            ...tracker.value,
            ...singleStepValues
        };
    }
}

defineExpose({ submit, reset });

watch(() => props.modelValue, loadDataFromProps);
</script>

<style scoped>

</style>
