<template>
    <canvas :id="id" />
</template>

<script setup lang="ts">
import Chart from 'chart.js/auto';
import { onMounted, ref, watch } from 'vue';

interface Props {
    data: any
    options?: any
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
            ctx,
            {
                data: props.data,
                options: props.options ?? {}
            }
        );
    }
}

onMounted(makeChart);
watch(() => props.options, makeChart);
watch(() => props.data, makeChart);
</script>
