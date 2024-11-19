<template>
    <canvas ref="canvas" class="h-full w-full absolute overflow-hidden" />
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';

const canvas = ref<(InstanceType<typeof HTMLCanvasElement>|null)>(null);

const start = (new Date()).getTime();

function random (v: number) {
    const x = Math.sin(v) * 10000;
    return x - Math.floor(x);
}

function draw () {
    const context = canvas.value?.getContext('2d');
    if (canvas.value === null || !context) {
        return;
    }

    const width = canvas.value.getBoundingClientRect().width;
    const height = canvas.value.getBoundingClientRect().height;
    canvas.value.width = width * 0.9;
    canvas.value.height = height * 0.9;

    const square = height / 30;
    const spacing = (height - square * 21) / 21;

    context.fillStyle = 'rgba(0, 0, 0, 0.125)';

    context.rotate(0.05);
    context.translate(0, -height * 0.1);

    const xValue = (x: number) => spacing * 0.5 + (square + spacing) * x;
    const yValue = (y: number) => height - (square + spacing) * y - square - spacing * 0.5;

    const delta = ((new Date()).getTime() - start) / 1000;
    const deltaFract = delta - Math.floor(delta);

    for (let x = 0; x < Math.floor(width / (square + spacing)) + 2; x++) {
        for (let y = 0; y < 21; y++) {
            const value = 1 - random((x + Math.floor(delta)) * 432 + y * 542 + 837) * 2;

            context.fillStyle = 'white';
            context.fillRect(xValue(x - deltaFract), yValue(y), square, square);
            context.fillStyle = (value >= 0 ? `rgba(25, 135, 84, ${value})` : `rgba(220, 53, 69, ${-value})`);
            context.fillRect(xValue(x - deltaFract), yValue(y), square, square);

            context.fillRect(xValue(x - deltaFract), yValue(y), square, square);
        }
    }
}

onMounted(() => {
    draw();
    addEventListener('resize', draw);
    setInterval(draw, 10);
});
</script>
