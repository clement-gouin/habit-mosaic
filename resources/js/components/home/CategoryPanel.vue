<template>
    <div v-if="trackers.length" class="p-1 p-sm-2 p-md-4">
        <h2 class="w-100 text-center">
            <i class="fa-xs " v-if="category.icon" :class="mapToClassName(category.icon)"></i>
            {{ category.name }}
            <small class="superscript fs-6 text-dark-emphasis rounded border p-1" :style="{backgroundColor: color('bg-subtle'), borderColor: color('border-subtle'), color: color('text-emphasis')}">{{ score.toFixed(1) }}</small>
        </h2>
        <div class="d-flex flex-row flex-wrap justify-content-center">
            <tracker-input
                v-for="(tracker,i) in trackers"
                v-bind:key="tracker.id"
                class="me-1 me-lg-2 mb-1 mb-lg-2"
                v-model="trackers[i]"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { Category, Tracker } from '@interfaces';
import { computed, ref, watch } from 'vue';
import { mapToClassName } from '@utils/icons';
import TrackerInput from './TrackerInput.vue';
import { referenceColor } from '@utils/colors';

interface Props {
    modelValue?: Category,
    trackers: Tracker[]
}

const props = defineProps<Props>();

const DEFAULT_CATEGORY = { name: 'Other', order: 0 };

const category = ref<Category>(props.modelValue ?? DEFAULT_CATEGORY);
const trackers = ref<Tracker[]>(props.trackers);
const score = computed<number>(() => trackers.value.map(tracker => tracker.data_point.score).reduce((a, b) => a + b, 0));

// TODO compute average per category
const color = variable => referenceColor(score.value, 1, variable);

watch(() => props.modelValue, () => {
    category.value = props.modelValue ?? DEFAULT_CATEGORY;
});
</script>

<style scoped>

</style>
