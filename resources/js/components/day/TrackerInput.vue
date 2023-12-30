<template>
    <div
        :style="{color: color('text-emphasis')}"
        class="p-0 fs-6 text-nowrap shadow-sm rounded-pill text-center position-relative user-select-none lh-1"
        :title="`${tracker.name}: ${rawValue.toFixed(precision(tracker.value_step))} ${tracker.unit}`.trim()"
    >
        <template v-if="tracker.single && (!tracker.overflow || rawValue < tracker.value_step)">
            <span v-if="rawValue" @click="remove" role="button">
                <span :style="{backgroundColor: color('bg-subtle'), borderColor: color('border-subtle')}" class="d-inline-block shadow-sm align-bottom h-100 border border-2 px-3 py-2 lh-base rounded-start-pill"></span>
                <i class="d-inline-block border-end rounded-end-pill border-top border-bottom border-2 px-2 py-2" :style="{backgroundColor: colorDark('bg-subtle'), borderColor: color('border-subtle')}" :class="mapToClassName(tracker.icon)"></i>
            </span>
            <span v-else @click="add" role="button">
                <i class="d-inline-block border-start rounded-start-pill border-top border-bottom border-2 px-2 py-2" :style="{backgroundColor: colorDark('bg-subtle'), borderColor: color('border-subtle')}" :class="mapToClassName(tracker.icon)"></i>
                <span :style="{backgroundColor: color('bg-subtle'), borderColor: color('border-subtle')}" class="d-inline-block shadow-sm align-bottom h-100 border border-2 px-3 py-2 lh-base rounded-end-pill"></span>
            </span>
        </template>
        <template v-else>
            <i :style="{backgroundColor: colorDark('bg-subtle'), borderColor: color('border-subtle')}" class="fa-solid  fa-minus shadow-sm border border-2 px-2 py-2 rounded-start-pill" role="button" @click="remove"></i>
            <span :style="{backgroundColor: color('bg-subtle'), borderColor: color('border-subtle')}" class="d-inline-block border-top border-bottom border-2 px-2 py-2">
                <i class="d-inline-block" :class="mapToClassName(tracker.icon)"></i>
                <span class="d-inline-block ps-2">{{ rawValue.toFixed(precision(tracker.value_step)) }}</span>
            </span>
            <i :style="{backgroundColor: colorDark('bg-subtle'), borderColor: color('border-subtle')}" class="fa-solid fa-plus shadow-sm border border-2 px-2 py-2 rounded-end-pill" role="button" @click="add"></i>
        </template>
    </div>
</template>

<script setup lang="ts">
import { DataPoint, TrackerFull } from '@interfaces';
import { defineProps, ref, watch } from 'vue';
import { mapToClassName } from '@utils/icons';
import { darker, ratioColor } from '@utils/colors';
import { precision } from '@utils/numbers';
import { updateDataPoint } from '@requests/dataPoints';
import { useDebouncedRef } from '@composables/useDebouncedRef';

interface Props {
    modelValue: TrackerFull
}

const props = defineProps<Props>();

const tracker = ref<TrackerFull>(props.modelValue);

const value = useDebouncedRef(tracker.value.data_point.value, 500);
const rawValue = ref<number>(tracker.value.data_point.value);

const color = variable => ratioColor(rawValue.value / tracker.value.target_value, tracker.value.target_score >= 0, variable);
const colorDark = variable => darker(0.03, color(variable));

function remove () {
    if (rawValue.value > 0) {
        updateValue(rawValue.value - tracker.value.value_step);
    }
}

function add () {
    if (!tracker.value.single || tracker.value.overflow) {
        updateValue(rawValue.value + tracker.value.value_step);
    }
}

function updateValue (v: number) {
    rawValue.value = v;
    value.value = v;
    tracker.value.data_point.score = tracker.value.target_score * v / tracker.value.target_value;
}

function update (dataPoint: DataPoint) {
    tracker.value.data_point = dataPoint;
}

watch(value, () => {
    if (value.value !== tracker.value.data_point.value) {
        updateDataPoint({ ...tracker.value.data_point, value: value.value })
            .then(update);
    }
});

watch(() => props.modelValue, () => {
    tracker.value = props.modelValue;
    rawValue.value = tracker.value.data_point.value;
    value.value = tracker.value.data_point.value;
});
</script>

<style scoped>
</style>
