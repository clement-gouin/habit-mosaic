<template>
    <div class="p-2 sm:p-2 md:p-4 select-none flex flex-col h-screen">
        <h1 class="border-b pb-2 mb-4 text-3xl font-bold flex-none">
            <i class="fa-solid fa-chart-column" />&nbsp;Graphics
        </h1>
        <div class="w-100 flex space-x-2 flex-none">
            <tracker-input class="flex-1" name="tracker" label="Tracker" v-model="selectedTracker"/>
            <select-input class="flex-1" name="show" label="Values shown" :options="SHOW_OPTIONS" :disabled="selectedTracker === null" required v-model="selectedShow"/>
            <select-input class="flex-1" name="days" label="Time span" :options="daysOptions" required v-model="selectedDays"/>
            <select-input class="flex-1" name="reduce" label="X axis" :options="REDUCE_OPTIONS"  required v-model="selectedReduce"/>
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
import { ratioColor } from '@utils/colorsRaw';
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
const monthChunks = ref<number[]>([]);
const loading = ref<boolean>(true);

const graphData = ref<ChartData>();
const graphOptions = ref<ChartOptions>();

const selectedTracker = ref<(TrackerFull|null)>(null);

const DAYS_OPTIONS_ALL: Option<number>[] = [
    {
        key: '2m',
        label: '2 months',
        value: -2
    },
    {
        key: '6m',
        label: '6 months',
        value: -6
    },
    {
        key: '1y',
        label: '1 year',
        value: -12
    },
    {
        key: '2y',
        label: '2 years',
        value: -24
    }
];

const daysOptions = computed<Option<number>[]>(() => {
    const options = DAYS_OPTIONS_ALL.filter((v, i) => i === 0 || (v.value < 0 ? -v.value * 30 <= props.max_days : v.value <= props.max_days));

    options.push({
        key: 'all',
        label: `All (${Math.round(props.max_days / 7).toFixed(0)} weeks)`,
        value: props.max_days
    });

    return options;
});

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

const REDUCE_OPTIONS: Option<boolean>[] = [
    {
        key: '0',
        label: 'Week averages',
        value: false
    },
    {
        key: '1',
        label: 'Month averages',
        value: true
    }
];

const selectedDays = ref<Option<number>>(daysOptions.value[0]);
const selectedShow = ref<Option<boolean>>(SHOW_OPTIONS[0]);
const selectedReduce = ref<Option<boolean>>(REDUCE_OPTIONS[0]);

function reduceChunks (data: (number|null)[], chunks: number[]): number[] {
    data = data.slice(-chunks.reduce((a, b) => a + b));
    return chunks.map(chunkSize => {
        const slice = data.splice(0, chunkSize).filter(i => i !== null) as number[];
        return slice.reduce((a, b) => a + b) / (slice.length ?? 1);
    });
}

function formatDate (date: Date, week: boolean): string {
    if (week) {
        return date.toLocaleDateString('en', { weekday: undefined, day: 'numeric', month: 'short', year: undefined }) + ' - ' + addDays(date, 6).toLocaleDateString('en', { weekday: undefined, day: 'numeric', month: 'short', year: undefined });
    }
    return date.toLocaleDateString('en', { weekday: undefined, day: undefined, month: 'short', year: '2-digit' });
}

function fetchData (): void {
    loading.value = true;
    const count = selectedDays.value.value;
    if (selectedTracker.value === null) {
        getDayGraphData(count < 0 ? undefined : count, count < 0 ? -count : undefined)
            .then(([newData, newMonthChunks]) => {
                rawData.value = newData;
                monthChunks.value = newMonthChunks;
                loading.value = false;
            });
    } else {
        getTrackerGraphData(selectedTracker.value, count < 0 ? undefined : count, count < 0 ? -count : undefined)
            .then(([newData, newMonthChunks]) => {
                rawData.value = newData;
                monthChunks.value = newMonthChunks;
                loading.value = false;
            });
    }
}

function makeGraphData (): void {
    let data = rawData.value.reverse();
    let globalAverage = selectedTracker.value?.statistics?.average ?? props.statistics.average;

    if (selectedTracker.value !== null && selectedShow.value.value) {
        data = data.map(v => v !== null ? (v * selectedTracker.value.target_value / selectedTracker.value.target_score) : v);
        globalAverage *= selectedTracker.value.target_value / selectedTracker.value.target_score;
    }

    const chunks = selectedReduce.value.value ? monthChunks.value : Array(Math.floor(data.length / 7)).fill(7);

    const reducedData = reduceChunks(data, chunks);

    graphOptions.value = {
        interaction: { mode: 'index' },
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                type: 'linear',
                min: Math.floor(Math.min(0, globalAverage, ...reducedData) * 1.1),
                max: Math.ceil(Math.max(0, globalAverage, ...reducedData) * 1.1)
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
            },
            annotation: {
                annotations: {
                    average: {
                        type: 'line',
                        borderColor: '#212529aa',
                        borderDash: [6, 6],
                        borderDashOffset: 0,
                        borderWidth: 3,
                        label: {
                            display: true,
                            content: (selectedTracker.value !== null && selectedShow.value.value) ? `Average: ${globalAverage.toFixed(precision(selectedTracker.value.value_step))} ${selectedTracker.value.unit ?? ''}` : `Average: ${globalAverage.toFixed(2)}`,
                            position: 'start'
                        },
                        scaleID: 'y',
                        value: globalAverage
                    }
                }
            }
        }
    };

    graphData.value = {
        labels: chunks
            .map((chunkSize, i) => (i < chunks.length - 1) ? chunks.slice(i + 1).reduce((a, b) => a + b) : 0)
            .map(v => formatDate(addDays(date.value, -v), !selectedReduce.value.value)),
        datasets: [
            {
                type: 'bar',
                label: selectedTracker.value ? `Week "${selectedTracker.value.name}" average` : 'Week average',
                data: reducedData,
                backgroundColor: reducedData.map(v => ratioColor(Math.abs(v / (globalAverage ?? 1)), v >= 0 && (selectedTracker.value ? selectedTracker.value.target_score >= 0 : true)))
            }
        ]
    };
}

watch(rawData, makeGraphData);
watch(selectedTracker, fetchData);
watch(selectedDays, fetchData);
watch(selectedShow, makeGraphData);
watch(selectedReduce, makeGraphData);

onBeforeMount(fetchData);
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>
