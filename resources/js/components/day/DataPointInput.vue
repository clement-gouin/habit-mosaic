<template>
    <div
        :style="{color: color('text-emphasis')}"
        class="p-0 fs-6 text-nowrap shadow-sm rounded-pill text-center position-relative user-select-none lh-1"
        :title="title"
    >
        <template v-if="tracker.single && (!tracker.overflow || rawValue < tracker.target_value)">
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
import { ref, watch, computed } from 'vue';
import { mapToClassName } from '@utils/icons';
import { darker, ratioColor } from '@utils/colors';
import { precision } from '@utils/numbers';
import { updateDataPoint } from '@requests/dataPoints';
import { useFullDebouncedRef } from '@composables/useFullDebouncedRef';

interface Props {
    modelValue: TrackerFull
}

const props = defineProps<Props>();

const tracker = ref<TrackerFull>(props.modelValue);

const loading = defineModel<boolean>('loading');
const updating = ref<boolean>(false);
const lastUpdated = ref<number>(Date.parse(props.modelValue.data_point.updated_at));

const { value, rawValue } = useFullDebouncedRef<number>(tracker.value.data_point.value, 500);

const isStale = computed(() => (!rawValue.value && tracker.value.stale_delay) ? (tracker.value.staleness ? tracker.value.staleness >= tracker.value.stale_delay : true) : false);
const title = computed(() => (`${tracker.value.name}: ${rawValue.value.toFixed(precision(tracker.value.value_step))} ${tracker.value.unit ?? ''}` + ((tracker.value.staleness && tracker.value.staleness > 0) ? `\nLast: ${tracker.value.staleness} days ago` : '')).trim());

const color = (variable: string) => isStale.value ? `var(--bs-warning-${variable}) !important` : ratioColor(rawValue.value / tracker.value.target_value, tracker.value.target_score >= 0, variable);
const colorDark = (variable: string) => darker(0.03, color(variable));

function remove () {
    if (rawValue.value > 0) {
        updateValue(rawValue.value - tracker.value.value_step);
    }
}

function add () {
    if (rawValue.value < tracker.value.target_value || tracker.value.overflow) {
        updateValue(rawValue.value + tracker.value.value_step);
    }
}

function updateValue (v: number) {
    updating.value = true;
    lastUpdated.value = Date.now();
    value.value = v;
    tracker.value.data_point.score = tracker.value.target_score * v / tracker.value.target_value;
}

function update (dataPoint: DataPoint) {
    tracker.value.data_point = dataPoint;
}

watch(value, () => {
    loading.value = true;
    if (updating.value) {
        updateDataPoint({ ...tracker.value.data_point, value: value.value })
            .then(update)
            .finally(() => {
                loading.value = false;
                updating.value = false;
            });
    }
});

watch(() => props.modelValue, () => {
    const updated = Date.parse(props.modelValue.data_point.updated_at);
    if (tracker.value.data_point.id === props.modelValue.data_point.id && !updating.value && lastUpdated.value < updated) {
        value.value = props.modelValue.data_point.value;
        lastUpdated.value = updated;
    }
    tracker.value = props.modelValue;
});
</script>

<style scoped>
</style>
