<template>
    <canvas ref="graph"></canvas>
</template>

<script setup lang="ts">
import { ChartData, ChartOptions } from 'chart.js/dist/types';
import Chart from 'chart.js/auto';
import { onMounted, ref, useTemplateRef } from 'vue';

interface Props {
    data: ChartData
    options?: ChartOptions
}

const props = defineProps<Props>();

const graphCanvas = useTemplateRef<HTMLCanvasElement>('graph');

const chart = ref<Chart|null>(null);

onMounted(() => {
    if (graphCanvas.value !== null) {
        chart.value = new Chart(
            graphCanvas.value,
            {
                data: props.data,
                options: props.options ?? {}
            }
        );
    }
});
</script>

<style scoped>

</style>
