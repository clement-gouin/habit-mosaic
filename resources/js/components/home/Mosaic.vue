<template>
    <canvas ref="canvas" />
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';

interface Props {
    data: (number|null)[]
    loading?: boolean
}

const props = defineProps<Props>();

interface Emits {
    (e: 'changeResolution', days: number): void
}

const emit = defineEmits<Emits>();

const canvas = ref<(InstanceType<typeof HTMLCanvasElement>|null)>(null);
const days = ref(0);

function draw () {
    const context = canvas.value?.getContext('2d');
    if (canvas.value === null || !context) {
        return;
    }

    const notNullData: number[] = props.data.filter(d => d !== null);
    const maxValue = Math.max(Math.max(...notNullData), -Math.min(...notNullData));

    const width = canvas.value.getBoundingClientRect().width;
    const height = canvas.value.getBoundingClientRect().height;
    canvas.value.width = width;
    canvas.value.height = height;

    const square = height / 10;
    const spacing = (height - square * 7) / 7;

    const xValue = x => spacing * 0.5 + (square + spacing) * x;
    const yValue = y => height - (square + spacing) * y - square - spacing * 0.5;

    context.clearRect(0, 0, width, height);

    if (props.data.length) {
        props.data.forEach((value, i) => {
            const x = Math.floor(i / 7);
            const y = i % 7;
            if (value !== null) {
                context.fillStyle = 'white';
                context.fillRect(xValue(x), yValue(y), square, square);
                context.fillStyle = (value >= 0 ? `rgba(25, 135, 84, ${value / maxValue})` : `rgba(220, 53, 69, ${-value / maxValue})`);
                context.fillRect(xValue(x), yValue(y), square, square);
            }
        });

        const today = ((new Date()).getDay() + 6) % 7;

        context.strokeStyle = 'rgba(0, 0, 0, 0.125)';
        context.lineWidth = spacing * 0.5;
        context.strokeRect(xValue(0), yValue(6 - today), square, square);
    }

    const total = Math.floor(width / (square + spacing)) * 7;

    if (total > days.value) {
        days.value = total;
        emit('changeResolution', total);
    }
}

onMounted(() => {
    draw();
    addEventListener('resize', draw);
});

watch(() => props.data, draw);
</script>

<style scoped>

</style>
