<template>
    <div class="w-full sticky top-0 z-1 select-none">
        <h2 class="w-full h-14 text-3xl font-bold border-b border-1 m-0 flex justify-between md:justify-center gap-14" :style="{backgroundColor: backgroundColor(baseColor), borderColor: borderColor(baseColor), color: textColor(baseColor)}">
            <i class="fa-solid w-8 fa-caret-left text-end my-auto" role="button" @click="previous"></i>
            <span class="text-center inline-block my-auto">
                {{ formatDate(new Date(rawDate)) }}
                <span class="text-gray-600 superscript rounded">{{ loading ? '???' : score.toFixed(1) }}</span>
            </span>
            <i class="fa-solid w-8 fa-caret-right text-start my-auto" :class="isToday ? 'opacity-0 pointer-events-none' : ''" role="button" @click="next"></i>
        </h2>
    </div>
    <div class="position-relative select-none" style="min-height: calc(100% - 3.1em)">
        <motivation-banner :loading="loading" :is-today="isToday" :score="score" :statistics="statistics" />
        <category-panel
            v-for="(category,i) in categories"
            v-bind:key="category.id"
            v-model="categories[i]"
            :trackers="trackers.filter((tracker: Tracker) => tracker.category.id === category.id)"
            v-model:loading="loadingInternal"
        />
        <LoadingMask v-if="loading" />
    </div>
</template>

<script setup lang="ts">
import { CategoryFull, Statistics, Tracker, TrackerFull } from '@interfaces';
import { computed, ref, watch } from 'vue';
import { getDayData } from '@requests/day';
import { backgroundColor, borderColor, referenceColor, textColor } from '@utils/colors';
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

const baseColor = computed(() => referenceColor(score.value, averageScore.value));

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
