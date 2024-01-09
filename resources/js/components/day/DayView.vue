<template>
    <h2 class="w-100 border-bottom border-1 py-2 user-select-none m-0 row" :style="{backgroundColor: color('bg-subtle'), borderColor: color('border-subtle'), color: color('text-emphasis')}">
        <span class="d-none d-xl-block col-3"></span>
        <i class="fa-solid fa-caret-left col-1 col text-end" role="button" @click="previous"></i>
        <span class="col-10 col-xl-4 text-center">
            {{ (new Date(rawDate)).toLocaleDateString('en', { weekday: 'short', day: 'numeric', month: 'short' }) }}
            <span class="text-dark-emphasis superscript rounded" v-if="!loading">{{ score.toFixed(1) }}</span>
        </span>
        <i v-if="canShowNext" class="fa-solid fa-caret-right col-1 text-start" role="button" @click="next"></i>
    </h2>
    <div class="position-relative" style="min-height: 100%">
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
import { Category, TrackerFull } from '@interfaces';
import { computed, ref, watch } from 'vue';
import { getDayData } from '@requests/day';
import { referenceColor } from '@utils/colors';
import CategoryPanel from './CategoryPanel.vue';
import useIdleWatcher from '@composables/useIdleWatcher';
import LoadingMask from '@tools/LoadingMask.vue';
import { useDebouncedRef } from '@composables/useDebouncedRef';

interface Props {
    date: string,
    average: number,
    categories: Category[],
    trackers: TrackerFull[]
}

const props = defineProps<Props>();

const average = ref<number>(props.average);
const categories = ref<Category[]>(props.categories);
const trackers = ref<TrackerFull[]>(props.trackers);
const date = useDebouncedRef(Date.parse(props.date), 500);
const rawDate = ref<number>(Date.parse(props.date));
const loading = ref(false);

const score = computed<number>(() => trackers.value.map(tracker => tracker.data_point.score).reduce((a, b) => a + b, 0));
const averageScore = computed<number>(() => Math.max(0, trackers.value.map(tracker => tracker.target_score * tracker.average / tracker.target_value).reduce((a, b) => a + b, 0)));
const canShowNext = computed<boolean>(() => (new Date(rawDate.value)).setHours(0, 0, 0, 0) < (new Date()).setHours(0, 0, 0, 0));

const color = (variable: string) => referenceColor(score.value, averageScore.value, variable);

function getData () {
    loading.value = true;
    getDayData(new Date(date.value))
        .then(([newDate, newAverage, newCategories, newTrackers]) => {
            date.value = Date.parse(newDate);
            average.value = newAverage;
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
    rawDate.value = before.setDate(before.getDate() - 1);
    date.value = rawDate.value;
}

function next () {
    const before = new Date(rawDate.value);
    loading.value = true;
    rawDate.value = before.setDate(before.getDate() + 1);
    date.value = rawDate.value;
}

watch(date, getData);

useIdleWatcher(getData);
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>
