<template>

</template>

<script setup lang="ts">
import { Category, DataPoint, TrackerFull } from '@interfaces';

interface Props {
    categories: Category[],
    trackers: TrackerFull[],
    data: Record<number, DataPoint[]>
}

const props = defineProps<Props>();
</script>

<style scoped>

</style>
