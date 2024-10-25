<template>
    <div class="p-1 py-2 p-sm-2 p-md-4 user-select-none">
        <h1 class="border-bottom border-1 pb-2">
            <i class="fa-solid fa-chart-column" />&nbsp;Graphics
        </h1>
        <chart type="bar" :data="graphData" />
        <div class="w-100 pt-4 row align-items-center">
            <tracker-input class="col-4 offset-4" name="tracker" label="" input-wrapper-col-size="12" v-model="selectedTracker"/>
        </div>
    </div>
</template>

<script setup lang="ts">
import Chart from '@components/graph/Chart.vue';
import { ref, watch } from 'vue';
import { addDays } from '@utils/dates';
import TrackerInput from '@components/trackers/TrackerInput.vue';
import { TrackerFull } from '@interfaces';
import { getDayGraphData, getTrackerGraphData } from '@requests/graph';
import { ChartData } from 'chart.js';

interface Props {
    date: string,
    data: (number|null)[]
    average: (number)[]
}

const props = defineProps<Props>();

const date = ref<number>(Date.parse(props.date));
const data = ref<(number|null)[]>(props.data);
const average = ref<(number)[]>(props.average);

const selectedTracker = ref<(TrackerFull|null)>(null);

function reduceChunks (data: (number|null)[], chunkSize: number): number[] {
    const output = [];

    data = data.slice().reverse();

    for (let i = 0; i < data.length; i += chunkSize) {
        const slice = data.slice(i, i + chunkSize).filter(i => i !== null);
        output.push(slice.reduce((a, b) => a + b) / (slice.length ?? 1));
    }

    return output;
}

function formatDate (date: Date): string {
    return date.toLocaleDateString('en', { weekday: undefined, day: 'numeric', month: 'short', year: undefined }) + ' - ' + addDays(date, 6).toLocaleDateString('en', { weekday: undefined, day: 'numeric', month: 'short', year: undefined });
}

function fetchData (): void {
    const days = 70;
    if (selectedTracker.value === null) {
        getDayGraphData(days)
            .then(([newData, newAverage]) => {
                data.value = newData;
                average.value = newAverage;
            });
    } else {
        getTrackerGraphData(selectedTracker.value, days)
            .then(([newData, newAverage]) => {
                data.value = newData;
                average.value = newAverage;
            });
    }
}

function makeGraphData (): void {
    graphData.value = {
        labels: average.value.map((v, i) => formatDate(addDays(date.value, -7 * (average.value.length - i + 1)))),
        datasets: [
            {
                type: 'line',
                label: 'Global average',
                data: average.value.slice().reverse(),
                borderColor: '#C2185B'
            },
            {
                type: 'bar',
                label: 'Week average',
                data: reduceChunks(data.value, 7),
                backgroundColor: '#EC407A'
            }
        ]
    };
}

const graphData = ref<ChartData>();

makeGraphData();

watch(data, makeGraphData);
watch(average, makeGraphData);
watch(selectedTracker, fetchData);
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>

<style scoped>

</style>
