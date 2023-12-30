<template>
    <div v-if="trackers.length" class="p-1 p-sm-2 p-md-4">
        <h3 class="w-100 text-center">
            <category-label :category="category"/>
            <small class="superscript fs-6 text-dark-emphasis rounded border p-1 ms-1" :style="{backgroundColor: color('bg-subtle'), borderColor: color('border-subtle'), color: color('text-emphasis')}">{{ score.toFixed(1) }}</small>
        </h3>
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
import { Category, TrackerFull } from '@interfaces';
import { computed, ref, watch } from 'vue';
import TrackerInput from './TrackerInput.vue';
import { referenceColor } from '@utils/colors';
import CategoryLabel from '../categories/CategoryLabel.vue';

interface Props {
    modelValue?: Category,
    trackers: TrackerFull[]
}

const props = defineProps<Props>();

const DEFAULT_CATEGORY = { name: 'Other', order: 0 };

const category = ref<Category>(props.modelValue ?? DEFAULT_CATEGORY);
const trackers = ref<TrackerFull[]>(props.trackers);
const score = computed<number>(() => trackers.value.map(tracker => tracker.data_point.score).reduce((a, b) => a + b, 0));

const averageScore = computed<number>(() => Math.max(0, trackers.value.map(tracker => tracker.target_score * tracker.average / tracker.target_value).reduce((a, b) => a + b, 0)));
const color = variable => referenceColor(score.value, averageScore.value, variable);

watch(() => props.modelValue, () => {
    category.value = props.modelValue ?? DEFAULT_CATEGORY;
});
</script>

<style scoped>

</style>
