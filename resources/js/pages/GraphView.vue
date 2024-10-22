<template>
    <div class="p-1 py-2 p-sm-2 p-md-4 user-select-none">
        <h1 class="border-bottom border-1 pb-2">
            <i class="fa-solid fa-chart-column" />&nbsp;Graphics
        </h1>
        <h3>🚧 Work in progress 🚧</h3>
        <chart type="bar" :data="DATA" />
    </div>
</template>

<script setup lang="ts">
import Chart from '@components/graph/Chart.vue';
import { ref } from 'vue';
import { addDays } from '@utils/dates';

interface Props {
    date: string,
    data: (number|null)[]
    average: (number)[]
}

const props = defineProps<Props>();

const date = ref<number>(Date.parse(props.date));

function reduceWeeks (data: (number|null)[]): number[] {
    const output = [];

    data = data.slice().reverse();

    for (let i = 0; i < data.length; i += 7) {
        const slice = data.slice(i, i + 7).filter(i => i !== null);
        output.push(slice.reduce((a, b) => a + b) / (slice.length ?? 1));
    }

    return output;
}

function formatDate (date: Date): string {
    return date.toLocaleDateString('en', { weekday: undefined, day: 'numeric', month: 'short', year: undefined }) + ' - ' + addDays(date, 6).toLocaleDateString('en', { weekday: undefined, day: 'numeric', month: 'short', year: undefined });
}

const DATA = {
    labels: props.average.map((v, i) => formatDate(addDays(date.value, -7 * (props.average.length - i + 1)))),
    datasets: [
        {
            type: 'line',
            label: 'Global average',
            data: props.average.slice().reverse(),
            borderColor: '#C2185B'
        },
        {
            type: 'bar',
            label: 'Week average',
            data: reduceWeeks(props.data),
            backgroundColor: '#EC407A'
        }
    ]
};
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>
