<template>
    <div v-if="trackers.length" class="px-1 px-sm-2 p py-3 p-md-4">
        <h3 class="w-100 text-center">
            <category-label :category="category"/>
            <score-badge :title="`average: ${category.statistics.average.toFixed(1)}`" :value="score" :reference="category.statistics.average" />
        </h3>
        <div class="d-flex flex-row flex-wrap justify-content-center">
            <data-point-input
                v-for="(tracker,i) in trackers"
                v-bind:key="tracker.id"
                class="me-1 me-lg-2 mb-1 mb-lg-2"
                v-model="trackers[i]"
                v-model:loading="loading"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { CategoryFull, TrackerFull } from '@interfaces';
import { computed, ref, watch } from 'vue';
import DataPointInput from './DataPointInput.vue';
import CategoryLabel from '@components/categories/CategoryLabel.vue';
import ScoreBadge from '@tools/ScoreBadge.vue';

interface Props {
    modelValue?: CategoryFull,
    trackers: TrackerFull[]
}

const props = defineProps<Props>();

const loading = defineModel<boolean>('loading');

// TODO compute real data or require category
const DEFAULT_CATEGORY = { id: 0, name: 'Other', order: 0, statistics: { total: 0, minimum: 0, average: 0, median: 0, maximum: 0 } };

const category = ref<CategoryFull>(props.modelValue ?? DEFAULT_CATEGORY);
const trackers = ref<TrackerFull[]>(props.trackers);
const score = computed<number>(() => trackers.value.map(tracker => tracker.data_point.score).reduce((a, b) => a + b, 0));

watch(() => props.modelValue, () => {
    category.value = props.modelValue ?? DEFAULT_CATEGORY;
    trackers.value = props.trackers;
});
</script>
