<template>
    <div :style="{color: textColor}" class="p-0 fs-3 text-nowrap text-center position-relative user-select-none lh-1" :title="tracker.name">
        <i :style="{backgroundColor: bgColor2, borderColor: borderColor}" class="fa-solid fa-minus border border-2 ps-3 pe-2 py-2 rounded-start-pill" role="button" @click="remove"></i>
        <span :style="{backgroundColor: bgColor, borderColor: borderColor}" class="d-inline-block border-top border-bottom border-2 px-3 py-2">
            <i class="d-inline-block" :class="mapToClassName(tracker.icon)"></i>
            <span v-if="!tracker.single" class="d-inline-block ps-2">{{ tracker.data_point.value.toFixed(precision(tracker.value_step)) }}</span>
        </span>
        <i :style="{backgroundColor: bgColor2, borderColor: borderColor}" class="fa-solid fa-plus border border-2 pe-3 ps-2 py-2 rounded-end-pill" role="button" @click="add"></i>
    </div>
</template>

<script setup lang="ts">
import { Tracker } from '@interfaces';
import { computed, defineProps, ref } from 'vue';
import { mapToClassName } from '@utils/icons';

interface Props {
    modelValue: Tracker
}

const props = defineProps<Props>();

const tracker = ref<Tracker>(props.modelValue);

const percent = computed(() => tracker.value.data_point.value > tracker.value.target_value ? 100 : 100 * tracker.value.data_point.value / tracker.value.target_value);

function color (v, darker = '0%') {
    return `color-mix(in srgb, var(--bs-dark) ${darker}, color-mix(in srgb, var(--bs-${tracker.value.target_score > 0 ? 'success' : 'danger'}-${v}) ${percent.value}%, var(--bs-light-${v}))) !important`;
}
const bgColor = computed(() => color('bg-subtle'));
const bgColor2 = computed(() => color('bg-subtle', '2%'));
const borderColor = computed(() => color('border-subtle'));
const textColor = computed(() => color('text-emphasis'));

function precision (a) {
    let e = 1;
    while (Math.round(a * e) / e !== a) e *= 10;
    return Math.log(e) / Math.LN10;
}

function remove () {
    if (tracker.value.data_point.value > 0) {
        tracker.value.data_point.value -= tracker.value.value_step;
    }
}

function add () {
    if (!tracker.value.single || tracker.value.data_point.value === 0) {
        tracker.value.data_point.value += tracker.value.value_step;
    }
}
</script>
