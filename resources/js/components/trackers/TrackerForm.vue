<template>
    <div>
        <category-input
            class="mb-2"
            name="category_id"
            label="Category"
            v-model="tracker.category"
            v-model:error="errors['category_id']"
            with-create-button
            required
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
            v-model="tracker.single"
            @change="onCheckSingle"
            toggle
        />
        <template v-if="!tracker.single">
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
            name="unit"
            label="Unit"
            v-model="tracker.unit"
            v-model:error="errors['unit']"
        />
        <checkbox-input
            class="mb-2"
            name="overflow"
            label="Tracker can overflow"
            v-model="tracker.overflow"
            toggle
        />
        <text-input
            class="mb-2"
            name="target_score"
            label="Target score"
            v-model="tracker.target_score"
            v-model:error="errors['target_score']"
            type="number"
            required
        />
        <text-input
            class="mb-2"
            name="stale_delay"
            label="Days before stale"
            v-model="tracker.stale_delay"
            v-model:error="errors['stale_delay']"
            type="number"
        />
    </div>
</template>

<script setup lang="ts">
import { Base, Tracker, TrackerData } from '@interfaces';
import { ref, watch } from 'vue';
import { handleFormErrors } from '@utils/forms';
import TextInput from '@tools/forms/TextInput.vue';
import IconInput from '@tools/forms/IconInput.vue';
import { createTracker, updateTracker } from '@requests/trackers';
import CheckboxInput from '@tools/forms/CheckboxInput.vue';
import CategoryInput from '@components/categories/CategoryInput.vue';

interface Props {
    modelValue?: TrackerData
}

const props = defineProps<Props>();

const singleStepValues: Base = {
    value_step: 1,
    target_value: 1,
    single: true,
    overflow: false
};

const formInitValues: TrackerData = {
    category: undefined,
    name: '',
    icon: '',
    target_score: 1,

    unit: 'times',
    value_step: 1,
    target_value: 1,
    single: true,
    overflow: false,
    stale_delay: undefined
};

const tracker = ref<TrackerData>(loadDataFromProps());
const errors = ref<Record<string, string>>({});

function reset () {
    errors.value = {};
    tracker.value = loadDataFromProps();
}

function loadDataFromProps (): TrackerData {
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
    } else {
        tracker.value.overflow = true;
    }
}

defineExpose({ submit, reset });

watch(() => props.modelValue, loadDataFromProps);
</script>
