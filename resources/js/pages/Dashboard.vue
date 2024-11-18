<template>
    <div class="p-1 py-2 sm:p-2 md:p-4 select-none">
        <h1 class="border-b pb-2 mb-4 text-3xl font-bold">
            <i class="fa-solid fa-grip" />&nbsp;Mosaic
            <score-badge :reference="1" :value="statistics.average" />
        </h1>
        <mosaic class="w-full" style="height: 10em" :data="daysData" @change-resolution="fetchDayData" />
        <hr class="my-4">
        <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4">
            <category-mosaic v-for="category in categories" v-bind:key="category.id" :category="category" />
        </div>
        <hr class="my-4">
        <div v-if="editSelectedTracker" class="mb-2">
            <tracker-input class="w-full md:w-1/2 lg:w-1/4" v-model="selectedTracker" no-clear>
                <template #right>
                    <div v-if="selectedTracker" class="cursor-pointer" @click="editSelectedTracker = false" title="Close"><i class="fa-solid fa-close"/></div>
                </template>
            </tracker-input>
        </div>
        <h3 v-else-if="selectedTracker" class="text-2xl font-bold mb-2">
            <tracker-label :tracker="selectedTracker" />
            <score-badge title="average value | score" :value="selectedTrackerAverage" :reference="selectedTracker.target_value" :precision="precision(selectedTracker.value_step)" :additional-value="selectedTracker.statistics?.average" />
            <span class="fs-6 ms-2 text-dark-emphasis btn btn-sm" title="edit" @click="editSelectedTracker = true"><i class="fa-solid fa-pencil"></i></span>
        </h3>
        <mosaic class="w-full" style="height: 10em" :tracker="selectedTracker" :data="selectedTrackerData" @change-resolution="fetchSelectedTrackerData" />
    </div>
</template>

<script setup lang="ts">
import { CategoryFull, Statistics, TrackerFull } from '@interfaces';
import Mosaic from '@components/home/Mosaic.vue';
import { computed, inject, onMounted, ref, watch } from 'vue';
import { getDayMosaicData, getTrackerMosaicData } from '@requests/mosaic';
import CategoryMosaic from '@components/home/CategoryMosaic.vue';
import TrackerInput from '@components/trackers/TrackerInput.vue';
import { VueCookies } from 'vue-cookies';
import TrackerLabel from '@components/trackers/TrackerLabel.vue';
import ScoreBadge from '@tools/ScoreBadge.vue';
import { precision } from '@utils/numbers';

interface Props {
    statistics: Statistics
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

const selectedTrackerAverage = computed<number>(() => (selectedTracker.value ? selectedTracker.value.target_value * selectedTracker.value.statistics.average / selectedTracker.value.target_score : 0));

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
