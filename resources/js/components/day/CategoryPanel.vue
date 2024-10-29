<template>
    <div v-if="trackers.length" class="px-1 sm:px-2 py-3 md:p-4">
        <h3 class="w-100 text-center text-2xl font-bold mb-2">
            <category-label :category="category"/>
            <score-badge :title="`average: ${category.statistics.average.toFixed(1)}`" :value="score" :reference="category.statistics.average" />
        </h3>
        <div class="flex flex-row flex-wrap justify-center gap-1 lg:gap-2">
            <data-point-input
                v-for="(tracker,i) in trackers"
                v-bind:key="tracker.id"
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

const DEFAULT_CATEGORY: CategoryFull = { id: 0, name: 'Other', order: 0, statistics: { total: 0, min: 0, average: 0, lower_quartile: 0, median: 0, upper_quartile: 0, max: 0 } };

const category = ref<CategoryFull>(props.modelValue ?? DEFAULT_CATEGORY);
const trackers = ref<TrackerFull[]>(props.trackers);
const score = computed<number>(() => trackers.value.map(tracker => tracker.data_point.score).reduce((a, b) => a + b, 0));

watch(() => props.modelValue, () => {
    category.value = props.modelValue ?? DEFAULT_CATEGORY;
    trackers.value = props.trackers;
});
</script>
