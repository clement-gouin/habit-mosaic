<template>
    <div class="p-2 sm:p-2 md:p-4 select-none">
        <h1 class="border-b pb-2 mb-4 text-3xl font-bold">
            <i class="fa-solid fa-chart-column" />&nbsp;Graphics
        </h1>
        <div class="w-100 flex space-x-2">
            <tracker-input class="flex-1" name="tracker" label="Tracker" v-model="selectedTracker"/>
            <select-input class="flex-1" name="days" label="Time span" :options="daysOptions" required v-model="selectedDays"/>
        </div>
        <chart class="h-fit" type="bar" :data="graphData" :options="graphOptions" />
    </div>
    <LoadingMask v-if="loading" />
</template>

<script setup lang="ts">
import Chart from '@components/graph/Chart.vue';
import { computed, onBeforeMount, ref, watch } from 'vue';
import { addDays } from '@utils/dates';
import TrackerInput from '@components/trackers/TrackerInput.vue';
import { Option, Statistics, TrackerFull } from '@interfaces';
import { getDayGraphData, getTrackerGraphData } from '@requests/graph';
import { ChartData } from 'chart.js';
import LoadingMask from '@tools/LoadingMask.vue';
import SelectInput from '@tools/forms/SelectInput.vue';
import { referenceColor } from '@utils/colorsRaw';
import { ChartOptions } from 'chart.js/auto';

interface Props {
    date: string,
    statistics: Statistics
    max_days: number,
}

const props = defineProps<Props>();

const date = ref<number>(Date.parse(props.date));
const data = ref<(number|null)[]>([]);
const average = ref<(number)[]>([]);
const loading = ref<boolean>(true);

const graphData = ref<ChartData>();
const graphOptions = ref<ChartOptions>();

const selectedTracker = ref<(TrackerFull|null)>(null);

const DAYS_OPTIONS_ALL: Option<number>[] = [
    {
        key: '2m',
        label: '2 months',
        value: 63
    },
    {
        key: '6m',
        label: '6 months',
        value: 189
    },
    {
        key: '1y',
        label: '1 year',
        value: 364
    },
    {
        key: '2y',
        label: '2 years',
        value: 728
    }
];

const daysOptions: Option<number>[] = computed<Option<number>[]>(() => DAYS_OPTIONS_ALL.filter((v, i) => i === 0 || v.value <= props.max_days));

const selectedDays = ref<Option<number>>(daysOptions.value[0]);

function reduceChunks (data: (number|null)[], chunkSize: number): number[] {
    const output = [];

    data = data.slice().reverse();

    for (let i = 0; i < data.length; i += chunkSize) {
        const slice = data.slice(i, i + chunkSize).filter(i => i !== null) as number[];
        output.push(slice.reduce((a, b) => a + b) / (slice.length ?? 1));
    }

    return output;
}

function formatDate (date: Date): string {
    return date.toLocaleDateString('en', { weekday: undefined, day: 'numeric', month: 'short', year: undefined }) + ' - ' + addDays(date, 6).toLocaleDateString('en', { weekday: undefined, day: 'numeric', month: 'short', year: undefined });
}

function fetchData (): void {
    loading.value = true;
    if (selectedTracker.value === null) {
        getDayGraphData(selectedDays.value.value)
            .then(([newData, newAverage]) => {
                data.value = newData;
                average.value = newAverage;
                loading.value = false;
            });
    } else {
        getTrackerGraphData(selectedTracker.value, selectedDays.value.value)
            .then(([newData, newAverage]) => {
                data.value = newData;
                average.value = newAverage;
                loading.value = false;
            });
    }
}

function makeGraphData (): void {
    const reducedData = reduceChunks(data.value, 7);
    const globalAverage = selectedTracker.value?.statistics?.average ?? props.statistics.average;

    graphOptions.value = {
        interaction: { mode: 'index' },
        scales: {
            y: {
                type: 'linear',
                min: Math.min(...average.value, ...reducedData) * 1.1,
                max: Math.max(...average.value, ...reducedData) * 1.1
            }
        }
    };

    graphData.value = {
        labels: average.value.map((v, i) => formatDate(addDays(date.value, -7 * (average.value.length - i + 1)))),
        datasets: [
            {
                type: 'line',
                label: 'Global average',
                data: average.value.slice().reverse(),
                borderColor: '#212529aa',
                backgroundColor: '#212529'
            },
            {
                type: 'bar',
                label: 'Week average',
                data: reducedData,
                backgroundColor: reducedData.map(v => referenceColor(v, globalAverage))
            }
        ]
    };
}

watch(data, makeGraphData);
watch(average, makeGraphData);
watch(selectedTracker, fetchData);
watch(selectedDays, fetchData);

onBeforeMount(fetchData);
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>
