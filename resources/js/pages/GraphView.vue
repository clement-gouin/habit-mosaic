<template>
    <div class="p-2 sm:p-2 md:p-4 select-none flex flex-col h-screen">
        <h1 class="border-b pb-2 mb-4 text-3xl font-bold flex-none">
            <i class="fa-solid fa-chart-column" />&nbsp;Graphics
        </h1>
        <div class="w-100 flex space-x-2 flex-none">
            <tracker-input class="md:basis-1/2 lg:basis-1/4" name="tracker" label="Tracker" v-model="selectedTracker"/>
            <select-input class="md:basis-1/2 lg:basis-1/4" name="days" label="Time span" :options="daysOptions" required v-model="selectedDays"/>
            <div class="lg:flex md:hidden basis-1/2 space-x-2 my-auto">
                <div class="flex-1 flex flex-col-reverse">
                    <checkbox-input :disabled="selectedTracker === null" name="value" label="Score" label-after="Tracker value" toggle v-model="showTrackerValue" />
                    <checkbox-input name="quartiles" label="Average" label-after="Quartiles" toggle v-model="showQuartiles" />
                </div>
                <div class="flex-1 flex flex-col-reverse">
                    <checkbox-input name="sum" label="Day average" label-after="Total" toggle invert v-model="reduceSum" />
                    <checkbox-input name="month" label="Weeks" label-after="Months" toggle invert v-model="reduceMonth" />
                </div>
            </div>
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
import CheckboxInput from '@tools/forms/CheckboxInput.vue';

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

const showTrackerValue = ref<boolean>(false);
const showQuartiles = ref<boolean>(false);
const reduceSum = ref<boolean>(false);
const reduceMonth = ref<boolean>(false);

const graphData = ref<ChartData>();
const graphOptions = ref<ChartOptions>();

const selectedTracker = ref<(TrackerFull|null)>(null);

const MONTH_LENGTH = 30.436875;

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
    const options = DAYS_OPTIONS_ALL.filter((v, i) => i === 0 || (v.value < 0 ? -v.value * MONTH_LENGTH <= props.max_days : v.value <= props.max_days));

    options.push({
        key: 'all',
        label: `All (${Math.round(props.max_days / 7).toFixed(0)} weeks)`,
        value: props.max_days
    });

    return options;
});

const selectedDays = ref<Option<number>>(daysOptions.value.slice(-1)[0]);

function reduceChunks (data: (number|null)[], chunks: number[]): number[] {
    data = data.slice(-chunks.reduce((a, b) => a + b));
    return chunks.map(chunkSize => {
        const slice = data.splice(0, chunkSize).filter(i => i !== null) as number[];
        return slice.reduce((a, b) => a + b) / (reduceSum.value ? 1 : (slice.length ?? 1));
    });
}

function formatDate (date: Date): string {
    if (!reduceMonth.value) {
        return date.toLocaleDateString('en', { weekday: undefined, day: 'numeric', month: 'short', year: undefined }) + ' - ' + addDays(date, 6).toLocaleDateString('en', { weekday: undefined, day: 'numeric', month: 'short', year: undefined });
    }
    return date.toLocaleDateString('en', { weekday: undefined, day: undefined, month: 'short', year: '2-digit' });
}

function fetchData (): Promise<void> {
    loading.value = true;
    const count = selectedDays.value.value;
    if (selectedTracker.value === null) {
        return getDayGraphData(count < 0 ? undefined : count, count < 0 ? -count : undefined)
            .then(([newData, newMonthChunks]) => {
                rawData.value = newData;
                monthChunks.value = newMonthChunks;
                loading.value = false;
            });
    } else {
        return getTrackerGraphData(selectedTracker.value, count < 0 ? undefined : count, count < 0 ? -count : undefined)
            .then(([newData, newMonthChunks]) => {
                rawData.value = newData;
                monthChunks.value = newMonthChunks;
                loading.value = false;
            });
    }
}

function makeGraphData (): void {
    let data = rawData.value.slice().reverse();
    const statistics: Statistics = { ...(selectedTracker.value?.statistics ?? props.statistics) };

    const statisticsKey = ['min', 'average', 'lower_quartile', 'median', 'upper_quartile', 'max'];

    if (selectedTracker.value !== null && showTrackerValue.value) {
        data = data.map(v => v !== null ? (v * selectedTracker.value.target_value / selectedTracker.value.target_score) : v);
        statisticsKey.forEach(key => {
            statistics[key] *= selectedTracker.value.target_value / selectedTracker.value.target_score;
        });

        if (selectedTracker.value.target_score < 0) {
            [statistics.lower_quartile, statistics.upper_quartile] = [statistics.upper_quartile, statistics.lower_quartile];
        }
    }

    if (reduceSum.value) {
        statisticsKey.forEach(key => {
            statistics[key] *= reduceMonth.value ? MONTH_LENGTH : 7;
        });
    }

    const chunks = reduceMonth.value ? monthChunks.value : Array(Math.floor(data.length / 7)).fill(7);

    const reducedData = reduceChunks(data, chunks);

    function showValue (v: number) {
        if (selectedTracker.value !== null && showTrackerValue.value) {
            return v.toFixed(reduceSum.value ? precision(selectedTracker.value.value_step) : 2) + ' ' + (selectedTracker.value.unit ?? '');
        } else {
            return v.toFixed(2);
        }
    }

    graphOptions.value = {
        interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
        },
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                type: 'linear',
                min: Math.floor(1.05 * Math.min(0, statistics.average, ...reducedData)),
                max: Math.ceil(1.05 * Math.max(0, statistics.average, ...reducedData))
            }
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        let label = context.dataset.label || '';

                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            label += showValue(context.parsed.y);
                        }

                        return label;
                    }
                }
            },
            annotation: {},
            background: {
                color: 'rgb(249, 250, 251)',
                icon: selectedTracker.value?.icon,
                text: selectedTracker.value?.name
            }
        }
    };

    if (showQuartiles.value) {
        graphOptions.value.plugins.annotation.annotations = {
            lower: {
                type: 'line',
                borderColor: '#212529aa',
                backgroundColor: '#212529aa',
                borderDash: [6, 6],
                borderDashOffset: 0,
                borderWidth: 3,
                label: {
                    display: true,
                    content: `75% ≥ ${showValue(statistics.lower_quartile)}`,
                    position: 'start'
                },
                scaleID: 'y',
                value: statistics.lower_quartile
            },
            median: {
                type: 'line',
                borderColor: '#212529aa',
                backgroundColor: '#212529aa',
                borderDash: [6, 6],
                borderDashOffset: 0,
                borderWidth: 3,
                label: {
                    display: true,
                    content: `50% ≥ ${showValue(statistics.median)}`,
                    position: 'start'
                },
                scaleID: 'y',
                value: statistics.median
            },
            upper: {
                type: 'line',
                borderColor: '#212529aa',
                backgroundColor: '#212529aa',
                borderDash: [6, 6],
                borderDashOffset: 0,
                borderWidth: 3,
                label: {
                    display: true,
                    content: `25% ≥ ${showValue(statistics.upper_quartile)}`,
                    position: 'start'
                },
                scaleID: 'y',
                value: statistics.upper_quartile
            }
        };
    } else {
        graphOptions.value.plugins.annotation.annotations = {
            average: {
                type: 'line',
                borderColor: '#212529aa',
                backgroundColor: '#212529aa',
                borderDash: [6, 6],
                borderDashOffset: 0,
                borderWidth: 3,
                label: {
                    display: true,
                    content: `Average ${reduceSum.value ? (reduceMonth.value ? 'month' : 'week') : 'day'}: ${showValue(statistics.average)}`,
                    position: 'start'
                },
                scaleID: 'y',
                value: statistics.average
            }
        };
    }

    graphData.value = {
        labels: chunks
            .map((chunkSize, i) => (i < chunks.length - 1) ? chunks.slice(i + 1).reduce((a, b) => a + b) : 0)
            .map(v => formatDate(addDays(date.value, -v))),
        datasets: [
            {
                type: 'bar',
                label: (reduceMonth.value ? 'Month ' : 'Week ') + (selectedTracker.value ? `"${selectedTracker.value.name}" ` : '') + (reduceSum.value ? 'total' : 'average'),
                data: reducedData,
                backgroundColor: reducedData.map(v => ratioColor(Math.abs(v / (statistics.average ?? 1)), v >= 0 && (selectedTracker.value ? selectedTracker.value.target_score >= 0 : true)))
            }
        ]
    };
}

watch(rawData, makeGraphData);
watch(selectedTracker, (newValue, oldValue) => {
    fetchData()
        .then(() => {
            if (newValue !== null && newValue !== oldValue) {
                showTrackerValue.value = true;
            }
            if (newValue === null && newValue !== oldValue) {
                showTrackerValue.value = false;
            }
        });
});
watch(selectedDays, fetchData);
watch(showTrackerValue, makeGraphData);
watch(showQuartiles, makeGraphData);
watch(reduceMonth, makeGraphData);
watch(reduceSum, makeGraphData);

onBeforeMount(fetchData);
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>
