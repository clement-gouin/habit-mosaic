<template>
    <canvas ref="canvas" :title="title" />
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { addDays } from '@utils/dates';
import { Tracker } from '@interfaces';
import { precision } from '@utils/numbers';

interface Props {
    data: (number|null)[]
    tracker?: Tracker
}

const props = defineProps<Props>();

interface Emits {
    (e: 'changeResolution', days: number): void
}

const emit = defineEmits<Emits>();

const canvas = ref<(InstanceType<typeof HTMLCanvasElement>|null)>(null);
const days = ref(0);
const title = ref<string|null>(null);
const mouseIn = ref<boolean>(false);
const mousePos = ref<{x:number, y:number}|null>(null);
const selected = ref(0);
const selectedDate = computed(() => addDays(Date.now(), -selected.value));

function openSelectedDate () {
    window.location.href = '/day?date=' + selectedDate.value.toISOString().split('T')[0];
}

function draw (consumePointerDown = false) {
    const context = canvas.value?.getContext('2d');
    if (canvas.value === null || !context) {
        return;
    }

    const notNullData: number[] = props.data.filter(d => d !== null);
    const maxValue = Math.max(Math.max(...notNullData), -Math.min(...notNullData));

    const rect = canvas.value.getBoundingClientRect();

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
        const today = ((new Date()).getDay() + 6) % 7;
        let selectedX = 0;
        let selectedY = 6 - today;

        if (mousePos.value) {
            selectedX = Math.floor((mousePos.value.x - rect.left) / (square + spacing));
            selectedY = Math.floor((height - mousePos.value.y + rect.top) / (square + spacing));
        }

        if (selectedX === 0 && selectedY < (6 - today)) {
            selectedY = 6 - today;
        }

        selected.value = selectedX * 7 + selectedY - (6 - today);

        if (consumePointerDown) {
            setTimeout(openSelectedDate, 100);
        }

        context.strokeStyle = 'rgba(0, 0, 0, 0.125)';
        context.lineWidth = spacing * 1;
        context.strokeRect(xValue(selectedX), yValue(selectedY), square, square);

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
    } else {
        context.fillStyle = 'rgba(0, 0, 0, 0.125)';

        for (let x = 0; x < Math.floor(width / (square + spacing)) + 1; x++) {
            for (let y = 0; y < 7; y++) {
                context.fillRect(xValue(x), yValue(y), square, square);
            }
        }
    }

    const total = Math.floor(width / (square + spacing)) * 7 + 7;

    if (total > days.value) {
        days.value = total;
        emit('changeResolution', total);
    }
}

onMounted(() => {
    draw();
    addEventListener('resize', () => draw());
    canvas.value?.addEventListener('mousemove', (evt: MouseEvent) => {
        if (mouseIn.value) {
            mousePos.value = {
                x: evt.clientX,
                y: evt.clientY
            };
            draw();
        }
    });
    canvas.value?.addEventListener('mouseout', () => {
        mouseIn.value = false;
        mousePos.value = null;
        draw();
    });
    canvas.value?.addEventListener('mouseover', () => {
        mouseIn.value = true;
        mousePos.value = null;
        draw();
    });
    canvas.value?.addEventListener('mousedown', (evt: MouseEvent) => {
        mousePos.value = {
            x: evt.clientX,
            y: evt.clientY
        };
        draw(true);
    });
});

watch(() => props.data, () => draw());
watch(selected, () => {
    const isCurrentYear = selectedDate.value.getFullYear() === (new Date()).getFullYear();
    const dateString = selectedDate.value.toLocaleDateString('en', { weekday: 'short', day: 'numeric', month: 'short', year: isCurrentYear ? undefined : 'numeric' });
    const score = props.data.filter(d => d !== null)[selected.value] ?? 0;
    if (props.tracker) {
        title.value = `${dateString} | value: ${(score * props.tracker.target_value / props.tracker.target_score).toFixed(precision(props.tracker.value_step))} | score: ${score.toFixed(1)}`;
    } else {
        title.value = `${dateString} | score: ${score.toFixed(1)}`;
    }
});
</script>
