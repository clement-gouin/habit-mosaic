<template>
    <div class="border border-2 rounded-pill px-2 py-1 fs-3 text-nowrap text-center position-relative" :title="tracker.name">
        <i class="fa-solid fa-minus" role="button" @click="tracker.data_point.value -= tracker.value_step"></i>
        <i class="border-start border-end border-2 px-1 mx-1" :class="mapToClassName(tracker.icon)"></i>
        <i class="fa-solid fa-plus" role="button" @click="tracker.data_point.value += tracker.value_step"></i>
        <div class="count text-center">
            <span class="fs-6 text-white border border-2 border-primary px-2 py-1 bg-secondary">{{ tracker.data_point.value }}</span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Tracker } from '@interfaces';
import { defineProps, ref } from 'vue';
import { mapToClassName } from '@utils/icons';

interface Props {
    modelValue: Tracker
}

const props = defineProps<Props>();

const tracker = ref<Tracker>(props.modelValue);
</script>

<style scoped>
.count {
    position: absolute;
    left: 0;
    right: 0;
    bottom: -50%;
}
</style>
