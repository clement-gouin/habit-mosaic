<template>
    <div
        :style="{color: textColor(baseColor)}"
        class="p-0 text-nowrap rounded-full h-fit text-center relative select-none lh-base"
        :title="title"
    >
        <template v-if="tracker.single && (!tracker.overflow || rawValue < tracker.target_value)">
            <span class="" v-if="rawValue" @click="remove" role="button">
                <span :style="{backgroundColor: backgroundColor(baseColor), borderColor: borderColor(baseColor)}" class="inline-block shadow border-2 px-3 py-2 lh-base rounded-l-full">&nbsp;</span>
                <i class="inline-block border-r rounded-r-full border-t border-b border-2 px-2 py-2 lh-base" :style="{backgroundColor: backgroundColor(baseColorDark), borderColor: borderColor(baseColor)}" :class="mapToClassName(tracker.icon)"></i>
            </span>
            <span v-else @click="add" role="button">
                <i class="inline-block border-l rounded-l-full border-t border-b border-2 px-2 py-2 lh-base" :style="{backgroundColor: backgroundColor(baseColorDark), borderColor: borderColor(baseColor)}" :class="mapToClassName(tracker.icon)"></i>
                <span :style="{backgroundColor: backgroundColor(baseColor), borderColor: borderColor(baseColor)}" class="inline-block shadow h-100 border-2 px-3 py-2 lh-base rounded-r-full">&nbsp;</span>
            </span>
        </template>
        <template v-else>
            <i :style="{backgroundColor: backgroundColor(baseColorDark), borderColor: borderColor(baseColor)}" class="fa-solid fa-minus shadow border-2 px-2 py-2 rounded-l-full lh-base" role="button" @click="remove"></i>
            <span :style="{backgroundColor: backgroundColor(baseColor), borderColor: borderColor(baseColor)}" class="inline-block border-t border-b border-2 px-2 py-2 lh-base">
                <i class="inline-block" :class="mapToClassName(tracker.icon)"></i>
                <span class="inline-block ps-2">{{ rawValue.toFixed(precision(tracker.value_step)) }}</span>
            </span>
            <i :style="{backgroundColor: backgroundColor(baseColorDark), borderColor: borderColor(baseColor)}" class="fa-solid fa-plus shadow border-2 px-2 py-2 rounded-r-full lh-base" role="button" @click="add"></i>
        </template>
    </div>
</template>

<script setup lang="ts">
import { DataPoint, TrackerFull } from '@interfaces';
import { ref, watch, computed } from 'vue';
import { mapToClassName } from '@utils/icons';
import { backgroundColor, borderColor, darker, textColor, ratioColor } from '@utils/colors';
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

const baseColor = computed(() => isStale.value ? 'oklch(var(--wa))' : ratioColor(rawValue.value / tracker.value.target_value, tracker.value.target_score >= 0));
const baseColorDark = computed(() => darker(0.1, baseColor.value));

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
    if (updating.value) {
        loading.value = true;
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
    if (!updating.value && (tracker.value.data_point.id !== props.modelValue.data_point.id || lastUpdated.value < updated)) {
        value.value = props.modelValue.data_point.value;
        lastUpdated.value = updated;
    }
    tracker.value = props.modelValue;
});
</script>

<style scoped>
.lh-base {
    line-height: 1.1rem;
}
</style>
