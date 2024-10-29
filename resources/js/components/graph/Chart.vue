<template>
    <canvas :id="id" />
</template>

<script setup lang="ts">
import Chart, { ChartItem, ChartOptions, ChartData, ChartConfiguration } from 'chart.js/auto';
import { onMounted, ref, watch } from 'vue';

interface Props {
    data: ChartData
    options?: ChartOptions
}

const props = defineProps<Props>();

const id = ref<string>('graph-' + Math.random().toString(36)
    .substring(2));

let chart: Chart|null = null;

function makeChart () {
    if (chart) {
        try {
            chart.destroy();
        } catch (error) {
            // ignore
        }
    }
    const ctx = document.getElementById(id.value);
    if (ctx !== null) {
        chart = new Chart(
            ctx as ChartItem,
            {
                data: props.data,
                options: props.options ?? {}
            } as ChartConfiguration
        );
    }
}

onMounted(makeChart);
watch(() => props.options, makeChart);
watch(() => props.data, makeChart);
</script>
