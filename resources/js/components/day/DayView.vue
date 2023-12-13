<template>
    <h1 class="w-100 text-center border-bottom border-1 py-2 user-select-none" :style="{backgroundColor: color('bg-subtle'), borderColor: color('border-subtle'), color: color('text-emphasis')}">
        <i class="fa-solid fa-caret-left" role="button" @click="previous"></i>
        {{ date.toLocaleDateString('en', { weekday: 'short', day: 'numeric', month: 'short' }) }}
        <span class="text-dark-emphasis superscript rounded">{{ score.toFixed(1) }}</span>
        <i v-if="canShowNext" class="fa-solid fa-caret-right" role="button" @click="next"></i>
    </h1>
    <category-panel
        v-for="(category,i) in categories"
        v-bind:key="category.id"
        v-model="categories[i]"
        :trackers="trackers.filter(tracker => tracker.category?.id === category.id)"
    />
    <category-panel
        :trackers="trackers.filter(tracker => tracker.category === null)"
    />
</template>

<script setup lang="ts">
import { Category, TrackerFull } from '@interfaces';
import { computed, ref } from 'vue';
import { getDayData } from '@requests/day';
import { referenceColor } from '@utils/colors';
import CategoryPanel from './CategoryPanel.vue';

interface Props {
    date: number,
    categories: Category[],
    trackers: TrackerFull[]
}

const props = defineProps<Props>();

const categories = ref<Category[]>(props.categories);
const trackers = ref<TrackerFull[]>(props.trackers);
const date = ref<Date>(new Date(props.date * 1000));
const score = computed<number>(() => trackers.value.map(tracker => tracker.data_point.score).reduce((a, b) => a + b, 0));

const averageScore = computed<number>(() => Math.max(0, trackers.value.map(tracker => tracker.target_score * tracker.average / tracker.target_value).reduce((a, b) => a + b, 0)));
const color = variable => referenceColor(score.value, averageScore.value, variable);

const canShowNext = computed<boolean>(() => date.value.setHours(0, 0, 0, 0) < (new Date()).setHours(0, 0, 0, 0));

function getData () {
    getDayData(date.value)
        .then(([newDate, newCategories, newTrackers]) => {
            date.value = new Date(newDate * 1000);
            categories.value = newCategories;
            trackers.value = newTrackers;
        });
}

function previous () {
    date.value.setDate(date.value.getDate() - 1);

    getData();
}

function next () {
    date.value.setDate(date.value.getDate() + 1);

    getData();
}

</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>

<style scoped>

</style>
