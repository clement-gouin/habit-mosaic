<template>
    <div class="p-1 py-2 p-sm-2 p-md-4 user-select-none">
        <h1 class="border-bottom border-1 pb-2">
            Dashboard
            <small title="average score" :class="`text-${color}-emphasis bg-${color}-subtle border-${color}-subtle`" class="superscript fs-6 text-dark-emphasis rounded border p-1 ms-1">{{ average.toFixed(1) }}</small>
        </h1>
        <mosaic class="w-100" style="height: 10em" :data="daysData" @change-resolution="fetchDayData" />
        <hr>
        <div class="row">
            <category-mosaic v-for="category in categories" v-bind:key="category.id" class="col-12 col-md-6" :category="category" />
        </div>
        <hr>
        <div class="row mb-2">
            <TrackerInput label="Specific tracker:" class="col-md-8 col-xxl-4" name="tracker" v-model="selectedTracker" />
        </div>
        <mosaic class="w-100" style="height: 10em" :data="selectedTrackerData" @change-resolution="fetchSelectedTrackerData" />
    </div>
</template>

<script setup lang="ts">
import { CategoryFull, Tracker, TrackerFull } from '@interfaces';
import Mosaic from './Mosaic.vue';
import { computed, inject, onMounted, ref, watch } from 'vue';
import { getDayMosaicData, getTrackerMosaicData } from '@requests/mosaic';
import CategoryMosaic from './CategoryMosaic.vue';
import TrackerInput from '../trackers/TrackerInput.vue';
import { VueCookies } from 'vue-cookies';

interface Props {
    average: number
    categories: CategoryFull[],
    trackers: TrackerFull[],
}

const props = defineProps<Props>();

const cookies: VueCookies = inject('$cookies');

const selectedTracker = ref<(Tracker|null)>(null);
const daysData = ref<(number|null)[]>([]);
const selectedTrackerData = ref<(number|null)[]>([]);
const selectedTrackerDays = ref<number>(0);

const color = computed(() => props.average > 0 ? 'success' : (props.average === 0 ? 'light' : 'danger'));

function fetchDayData (days: number) {
    getDayMosaicData(days)
        .then(data => {
            daysData.value = data;
        });
}

function fetchSelectedTrackerData (days: number) {
    selectedTrackerDays.value = days;
    if (selectedTracker.value) {
        getTrackerMosaicData(selectedTracker.value, days)
            .then(data => {
                selectedTrackerData.value = data;
            })
            .catch(() => {
                selectedTrackerData.value = [];
            });
    } else {
        selectedTrackerData.value = [];
    }
}

watch(selectedTracker, () => {
    cookies.set('dashboard.selected_tracker', selectedTracker.value?.id ?? null);
    fetchSelectedTrackerData(selectedTrackerDays.value);
});

onMounted(() => {
    const selectedTrackerId = parseInt(cookies.get('dashboard.selected_tracker'));
    if (selectedTrackerId) {
        selectedTracker.value = props.trackers.filter(tracker => tracker.id === selectedTrackerId)[0] ?? null;
    }
});
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>

<style scoped>

</style>
