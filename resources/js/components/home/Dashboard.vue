<template>
    <h1 class="w-100 text-center border-bottom border-1 pb-2 mb-3 user-select-none">
        <i class="fa-solid fa-caret-left" role="button" @click="previous"></i>
        {{ date.toLocaleDateString('en', { weekday: 'long', day: 'numeric', month: 'long' }) }}
        <i v-if="canShowNext" class="fa-solid fa-caret-right" role="button" @click="next"></i>
    </h1>
    <div class="d-flex flex-row flex-wrap justify-content-center">
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

interface Props {
    date: number,
    trackers: Tracker[]
}

const props = defineProps<Props>();

const trackers = ref<Tracker[]>(props.trackers);

const date = ref<Date>(new Date(props.date * 1000));

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
