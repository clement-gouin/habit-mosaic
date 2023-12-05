<template>
    <div :style="{color: textColor}" class="p-0 fs-5 text-nowrap text-center position-relative user-select-none lh-1" :title="tracker.name">
        <i :style="{backgroundColor: bgColor2, borderColor: borderColor}" class="fa-solid fa-minus border border-2 px-2 py-2 rounded-start-pill" role="button" @click="remove"></i>
        <span :style="{backgroundColor: bgColor, borderColor: borderColor}" class="d-inline-block border-top border-bottom border-2 px-2 py-2">
            <i class="d-inline-block" :class="mapToClassName(tracker.icon)"></i>
            <span v-if="!tracker.single" class="d-inline-block ps-2">{{ rawValue.toFixed(precision(tracker.value_step)) }}</span>
        </span>
        <i :style="{backgroundColor: bgColor2, borderColor: borderColor}" class="fa-solid fa-plus border border-2 px-2 py-2 rounded-end-pill" role="button" @click="add"></i>
    </div>
</template>

<script setup lang="ts">
import { DataPoint, Tracker } from '@interfaces';
import { computed, defineProps, ref, watch } from 'vue';
import { mapToClassName } from '@utils/icons';
import { updateDataPoint } from '@requests/dataPoints';
import { useDebouncedRef } from '@composables/useDebouncedRef';

interface Props {
    modelValue: Tracker
}

const props = defineProps<Props>();

const tracker = ref<Tracker>(props.modelValue);

const value = useDebouncedRef(tracker.value.data_point.value, 500);
const rawValue = ref<number>(tracker.value.data_point.value);

const percent = computed(() => rawValue.value > tracker.value.target_value ? 100 : 100 * rawValue.value / tracker.value.target_value);

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
    if (rawValue.value > 0) {
        rawValue.value -= tracker.value.value_step;
        value.value = rawValue.value;
    }
}

function add () {
    if (!tracker.value.single || rawValue.value === 0) {
        rawValue.value += tracker.value.value_step;
        value.value = rawValue.value;
    }
}

function update (dataPoint: DataPoint) {
    tracker.value.data_point = dataPoint;
}

watch(value, () => {
    updateDataPoint({ ...tracker.value.data_point, value: value.value })
        .then(update);
});
</script>
