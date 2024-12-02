<template>
    <div class="p-2 sm:p-2 md:p-4 select-none flex flex-col h-screen">
        <h1 class="border-b pb-2 mb-4 text-3xl font-bold flex-none">
            <i class="fa-solid fa-chart-column" />&nbsp;Graphics
        </h1>
        <div class="w-100 flex space-x-2 flex-none">
            <tracker-input class="flex-1" name="tracker" label="Tracker" v-model="selectedTracker"/>
            <select-input class="flex-1" name="days" label="Time span" :options="daysOptions" required v-model="selectedDays"/>
            <select-input class="flex-1" name="show" label="Values shown" :options="SHOW_OPTIONS" :disabled="selectedTracker === null" required v-model="selectedShow"/>
        </div>
        <div class="grow w-100">
            <chart type="bar" :data="graphData" :options="graphOptions" />
        </div>
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
import { precision } from '@utils/numbers';

interface Props {
    date: string,
    statistics: Statistics
    max_days: number,
}

const props = defineProps<Props>();

const date = ref<number>(Date.parse(props.date));
const rawData = ref<(number|null)[]>([]);
const rawAverage = ref<(number)[]>([]);
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

const daysOptions = computed<Option<number>[]>(() => DAYS_OPTIONS_ALL.filter((v, i) => i === 0 || v.value <= props.max_days));

const SHOW_OPTIONS: Option<boolean>[] = [
    {
        key: '0',
        label: 'Show score',
        value: false
    },
    {
        key: '1',
        label: 'Show tracker value',
        value: true
    }
];

const selectedDays = ref<Option<number>>(daysOptions.value[0]);
const selectedShow = ref<Option<boolean>>(SHOW_OPTIONS[0]);

function reduceChunks (data: (number|null)[], chunkSize: number): number[] {
    const output = [];

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
                rawData.value = newData;
                rawAverage.value = newAverage;
                loading.value = false;
            });
    } else {
        getTrackerGraphData(selectedTracker.value, selectedDays.value.value)
            .then(([newData, newAverage]) => {
                rawData.value = newData;
                rawAverage.value = newAverage;
                loading.value = false;
            });
    }
}

function makeGraphData (): void {
    let data = rawData.value.slice(1).reverse();
    let average = rawAverage.value.slice(1).reverse();

    if (selectedTracker.value !== null && selectedShow.value.value) {
        data = data.map(v => v !== null ? (v * selectedTracker.value.target_value / selectedTracker.value.target_score) : v);
        average = average.map(v => v * selectedTracker.value.target_value / selectedTracker.value.target_score);
    }

    const reducedData = reduceChunks(data, 7);
    const globalAverage = selectedTracker.value?.statistics?.average ?? props.statistics.average;

    graphOptions.value = {
        interaction: { mode: 'index' },
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                type: 'linear',
                min: Math.floor(Math.min(...average, ...reducedData) * 1.1),
                max: Math.ceil(Math.max(...average, ...reducedData) * 1.1)
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function (context) {
                        let label = context.dataset.label || '';

                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            if (selectedTracker.value !== null && selectedShow.value.value) {
                                label += context.parsed.y.toFixed(precision(selectedTracker.value.value_step)) + ' ' + (selectedTracker.value.unit ?? ' ');
                            } else {
                                label += context.parsed.y.toFixed(2);
                            }
                        }

                        return label;
                    }
                }
            }
        }
    };

    graphData.value = {
        labels: average.map((v, i) => formatDate(addDays(date.value, -7 * (average.length - i + 1)))),
        datasets: [
            {
                type: 'line',
                label: selectedTracker.value ? `Global "${selectedTracker.value.name}" average` : 'Global day average',
                data: average,
                borderColor: '#212529aa',
                backgroundColor: '#212529'
            },
            {
                type: 'bar',
                label: selectedTracker.value ? `Week "${selectedTracker.value.name}" average` : 'Week average',
                data: reducedData,
                backgroundColor: reducedData.map(v => referenceColor(v, globalAverage))
            }
        ]
    };
}

watch(rawData, makeGraphData);
watch(rawAverage, makeGraphData);
watch(selectedTracker, fetchData);
watch(selectedDays, fetchData);
watch(selectedShow, makeGraphData);

onBeforeMount(fetchData);
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>
