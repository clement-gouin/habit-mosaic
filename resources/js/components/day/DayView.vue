<template>
    <div class="w-100 position-sticky top-0 z-1 user-select-none">
        <h2 class="w-100 border-bottom border-1 py-2 m-0 row" :style="{backgroundColor: color('bg-subtle'), borderColor: color('border-subtle'), color: color('text-emphasis')}">
            <span class="d-none d-xl-block col-3"></span>
            <i class="fa-solid fa-caret-left col-1 col text-end" role="button" @click="previous"></i>
            <span class="col-10 col-xl-4 text-center">
                {{ (new Date(rawDate)).toLocaleDateString('en', { weekday: 'short', day: 'numeric', month: 'short' }) }}
                <span class="text-dark-emphasis superscript rounded" v-if="!loading">{{ score.toFixed(1) }}</span>
            </span>
            <i v-if="!isToday" class="fa-solid fa-caret-right col-1 text-start" role="button" @click="next"></i>
        </h2>
    </div>
    <div class="position-relative user-select-none" style="min-height: calc(100% - 3.1em)">
        <motivation-banner v-if="isToday && !loading" :score="score" :statistics="statistics" />
        <category-panel
            v-for="(category,i) in categories"
            v-bind:key="category.id"
            v-model="categories[i]"
            :trackers="trackers.filter(tracker => tracker.category?.id === category.id)"
        />
        <category-panel
            :trackers="trackers.filter(tracker => tracker.category === null)"
        />
        <LoadingMask v-if="loading" />
    </div>
</template>

<script setup lang="ts">
import { CategoryFull, Statistics, TrackerFull } from '@interfaces';
import { computed, ref, watch } from 'vue';
import { getDayData } from '@requests/day';
import { referenceColor } from '@utils/colors';
import CategoryPanel from './CategoryPanel.vue';
import useIdleWatcher from '@composables/useIdleWatcher';
import LoadingMask from '@tools/LoadingMask.vue';
import { useFullDebouncedRef } from '@composables/useFullDebouncedRef';
import MotivationBanner from './MotivationBanner.vue';

interface Props {
    date: string,
    statistics: Statistics
    categories: CategoryFull[],
    trackers: TrackerFull[]
}

const props = defineProps<Props>();

const statistics = ref<Statistics>(props.statistics);
const categories = ref<CategoryFull[]>(props.categories);
const trackers = ref<TrackerFull[]>(props.trackers);
const { value: date, rawValue: rawDate } = useFullDebouncedRef<number>(Date.parse(props.date), 500);
const loading = ref(false);

const score = computed<number>(() => trackers.value.map(tracker => tracker.data_point.score).reduce((a, b) => a + b, 0));
const averageScore = computed<number>(() => Math.max(0, trackers.value.map(tracker => tracker.statistics.average).reduce((a, b) => a + b, 0)));
const isToday = computed<boolean>(() => (new Date(rawDate.value)).setHours(0, 0, 0, 0) === (new Date()).setHours(0, 0, 0, 0));

const wasToday = ref<boolean>(isToday.value);

const color = (variable: string) => referenceColor(score.value, averageScore.value, variable);

function getData () {
    loading.value = true;
    getDayData(new Date(date.value))
        .then(([newDate, newStatistics, newCategories, newTrackers]) => {
            date.value = Date.parse(newDate);
            wasToday.value = isToday.value;
            statistics.value = newStatistics;
            categories.value = newCategories;
            trackers.value = newTrackers;
        })
        .finally(() => {
            loading.value = false;
        });
}

function previous () {
    const before = new Date(rawDate.value);
    loading.value = true;
    date.value = before.setDate(before.getDate() - 1);
}

function next () {
    const before = new Date(rawDate.value);
    loading.value = true;
    date.value = before.setDate(before.getDate() + 1);
}

watch(date, getData);

useIdleWatcher(() => {
    if (wasToday.value && !isToday.value) {
        date.value = (new Date()).setHours(((new Date(date.value)).getHours()), 0, 0, 0);
    } else {
        getData();
    }
});
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>
