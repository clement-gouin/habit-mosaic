<template>
    <h1 class="w-100 text-center border-bottom border-1 py-2 user-select-none" :style="{backgroundColor: color('bg-subtle'), borderColor: color('border-subtle'), color: color('text-emphasis')}">
        <i class="fa-solid fa-caret-left" role="button" @click="previous"></i>
        {{ date.toLocaleDateString('en', { weekday: 'short', day: 'numeric', month: 'short' }) }}
        <span class="text-dark-emphasis">({{ score.toFixed(1) }})</span>
        <i v-if="canShowNext" class="fa-solid fa-caret-right" role="button" @click="next"></i>
    </h1>
    <div class="d-flex flex-row flex-wrap justify-content-center p-1 p-sm-2 p-md-4">
        <template v-for="(tracker,i) in trackers" v-bind:key="tracker.id">
            <tracker-input class="me-1 me-lg-2 mb-1 mb-lg-2" v-model="trackers[i]" />
        </template>
    </div>
</template>

<script setup lang="ts">
import { Tracker } from '@interfaces';
import TrackerInput from '@tools/TrackerInput.vue';
import { computed, ref } from 'vue';
import { getDashboardData } from '@requests/dashboard';
import { referenceColor } from '@utils/colors';

interface Props {
    date: number,
    trackers: Tracker[]
}

const props = defineProps<Props>();

const trackers = ref<Tracker[]>(props.trackers);
const date = ref<Date>(new Date(props.date * 1000));
const score = computed<number>(() => trackers.value.map(tracker => tracker.data_point.score).reduce((a, b) => a + b, 0));

// TODO compute average day
const color = (variable, darker = '0%') => referenceColor(score.value, 10, variable, darker);

const canShowNext = computed<boolean>(() => date.value.setHours(0, 0, 0, 0) < (new Date()).setHours(0, 0, 0, 0));

function previous () {
    date.value.setDate(date.value.getDate() - 1);

    getDashboardData(date.value)
        .then(([newDate, newTrackers]) => {
            date.value = new Date(newDate * 1000);
            trackers.value = newTrackers;
        });
}

function next () {
    date.value.setDate(date.value.getDate() + 1);

    getDashboardData(date.value)
        .then(([newDate, newTrackers]) => {
            date.value = new Date(newDate * 1000);
            trackers.value = newTrackers;
        });
}

</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>

<style scoped>

</style>
