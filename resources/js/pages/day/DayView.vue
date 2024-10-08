<template>
    <div class="w-100 position-sticky top-0 z-1 user-select-none">
        <h2 class="w-100 border-bottom border-1 py-2 m-0 row" :style="{backgroundColor: color('bg-subtle'), borderColor: color('border-subtle'), color: color('text-emphasis')}">
            <span class="d-none d-xl-block col-3"></span>
            <i class="fa-solid fa-caret-left col-1 col text-end" role="button" @click="previous"></i>
            <span class="col-10 col-xl-4 text-center">
                {{ formatDate(new Date(rawDate)) }}
                <span class="text-dark-emphasis superscript rounded" v-if="!loading">{{ score.toFixed(1) }}</span>
            </span>
            <i v-if="!isToday" class="fa-solid fa-caret-right col-1 text-start" role="button" @click="next"></i>
        </h2>
    </div>
    <div class="position-relative user-select-none" style="min-height: calc(100% - 3.1em)">
        <motivation-banner :loading="loading" :is-today="isToday" :score="score" :statistics="statistics" />
        <category-panel
            v-for="(category,i) in categories"
            v-bind:key="category.id"
            v-model="categories[i]"
            :trackers="trackers.filter(tracker => tracker.category.id === category.id)"
            v-model:loading="loadingInternal"
        />
        <LoadingMask v-if="loading" />
    </div>
</template>

<script setup lang="ts">
import { CategoryFull, Statistics, TrackerFull } from '@interfaces';
import { computed, ref, watch } from 'vue';
import { getDayData } from '@requests/day';
import { referenceColor } from '@utils/colors';
import CategoryPanel from '@components/day/CategoryPanel.vue';
import LoadingMask from '@tools/LoadingMask.vue';
import { useFullDebouncedRef } from '@composables/useFullDebouncedRef';
import MotivationBanner from '@components/day/MotivationBanner.vue';
import { formatDate } from '@utils/dates';
import { useBackgroundFetch } from '@composables/useBackgroundFetch';

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

const color = (variable: string) => referenceColor(score.value, averageScore.value, variable);

const { loading: loadingInternal, forceFetch } = useBackgroundFetch(async () => getDayData(new Date(date.value)), ([newDate, newStatistics, newCategories, newTrackers]) => {
    date.value = Date.parse(newDate);
    statistics.value = newStatistics;
    categories.value = newCategories;
    trackers.value = newTrackers;
    loading.value = false;
}, 10 * 1000, false);

function previous () {
    const before = new Date(rawDate.value);
    date.value = before.setDate(before.getDate() - 1);
}

function next () {
    const before = new Date(rawDate.value);
    date.value = before.setDate(before.getDate() + 1);
}

// function enforceToday () {
//     if (loading.value || loadingInternal.value) {
//         return;
//     }
//     const params = new URL(document.location.toString()).searchParams;
//     if (!params.has('date')) {
//         date.value = (new Date()).setHours(((new Date(date.value)).getHours()), 0, 0, 0);
//     }
// }

watch(rawDate, () => {
    loading.value = true;
    loadingInternal.value = true;
});

watch(date, forceFetch);

// onBeforeMount(() => {
//     setInterval(enforceToday, 100);
// });
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>
