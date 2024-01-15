<template>
    <div class="p-1 py-2 p-sm-2 p-md-4 user-select-none">
        <h1 class="border-bottom border-1 pb-2">
            Dashboard
            <small title="average score" :class="`text-${color}-emphasis bg-${color}-subtle border-${color}-subtle`" class="superscript fs-6 text-dark-emphasis rounded border p-1 ms-1">
                {{ average.toFixed(1) }}
            </small>
        </h1>
        <mosaic class="w-100" style="height: 10em" :data="daysData" @change-resolution="fetchDayData" />
        <hr>
        <div class="row">
            <category-mosaic v-for="category in categories" v-bind:key="category.id" class="col-12 col-md-6" :category="category" />
        </div>
        <hr>
        <div v-if="editSelectedTracker" class="row mb-2">
            <TrackerInput class="col-md-8 col-xxl-4" name="tracker" v-model="selectedTracker">
                <template #label><span></span></template>
                <template #addon>
                    <div v-if="selectedTracker" class="input-group-addon text-dark-emphasis btn btn-light border" @click="editSelectedTracker = false" title="Close"><i class="fa-solid fa-close"/></div>
                </template>
            </TrackerInput>
        </div>
        <h3 v-else-if="selectedTracker">
            <tracker-label :tracker="selectedTracker" />
            <small title="average value / score" :class="`text-${colorTracker}-emphasis bg-${colorTracker}-subtle border-${colorTracker}-subtle`" class="superscript fs-6 text-dark-emphasis rounded border p-1 ms-1">
                {{ selectedTracker.average.toFixed(1) }} | {{ selectedTrackerAverage.toFixed(1) }}
            </small>
            <span class="fs-6 ms-2 text-dark-emphasis btn" title="edit" @click="editSelectedTracker = true"><i class="fa-solid fa-pencil"></i></span>
        </h3>
        <mosaic class="w-100" style="height: 10em" :tracker="selectedTracker" :data="selectedTrackerData" @change-resolution="fetchSelectedTrackerData" />
    </div>
</template>

<script setup lang="ts">
import { CategoryFull, TrackerFull } from '@interfaces';
import Mosaic from './Mosaic.vue';
import { computed, inject, onMounted, ref, watch } from 'vue';
import { getDayMosaicData, getTrackerMosaicData } from '@requests/mosaic';
import CategoryMosaic from './CategoryMosaic.vue';
import TrackerInput from '../trackers/TrackerInput.vue';
import { VueCookies } from 'vue-cookies';
import TrackerLabel from '../trackers/TrackerLabel.vue';

interface Props {
    average: number
    categories: CategoryFull[],
    trackers: TrackerFull[],
}

const props = defineProps<Props>();

const cookies: VueCookies | undefined = inject('$cookies');

const editSelectedTracker = ref<boolean>(false);
const selectedTracker = ref<(TrackerFull|null)>(null);
const daysData = ref<(number|null)[]>([]);
const selectedTrackerData = ref<(number|null)[]>([]);
const selectedTrackerDays = ref<number>(0);

const color = computed(() => props.average > 0 ? 'success' : (props.average === 0 ? 'light' : 'danger'));
const selectedTrackerAverage = computed<number>(() => (selectedTracker.value ? selectedTracker.value.average * selectedTracker.value.target_score / selectedTracker.value.target_value : 0));
const colorTracker = computed(() => selectedTrackerAverage.value > 0 ? 'success' : (selectedTrackerAverage.value === 0 ? 'light' : 'danger'));

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
    selectedTracker.value = props.trackers.filter(tracker => tracker.id === selectedTracker.value?.id)[0] ?? null;
    cookies?.set('dashboard.selected_tracker', selectedTracker.value?.id ?? null);
    fetchSelectedTrackerData(selectedTrackerDays.value);
});

onMounted(() => {
    const selectedTrackerId = parseInt(cookies?.get('dashboard.selected_tracker'));
    if (selectedTrackerId) {
        selectedTracker.value = props.trackers.filter(tracker => tracker.id === selectedTrackerId)[0] ?? null;
    }
    editSelectedTracker.value = selectedTracker.value === null;
});
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>

<style scoped>

</style>
