<template>
    <div class="p-1 p-sm-2 p-md-4">
        <h1 class="border-bottom border-1 pb-2">
            Dashboard
            <small title="average score" :class="`text-${color}-emphasis bg-${color}-subtle border-${color}-subtle`" class="superscript fs-6 text-dark-emphasis rounded border p-1 ms-1">{{ average.toFixed(1) }}</small>
        </h1>
        <mosaic class="w-100" style="height: 10em" :data="daysData" @change-resolution="fetchDayData" />
        <hr>
        <div class="row">
            <category-mosaic v-for="category in categories" v-bind:key="category.id" class="col-12 col-md-6" :category="category" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { CategoryFull, TrackerFull } from '@interfaces';
import Mosaic from './Mosaic.vue';
import { computed, ref } from 'vue';
import { getDayMosaicData } from '@requests/mosaic';
import CategoryMosaic from './CategoryMosaic.vue';

interface Props {
    average: number
    categories: CategoryFull[],
    trackers: TrackerFull[],
}

const props = defineProps<Props>();

const daysData = ref<(number|null)[]>([]);

const color = computed(() => props.average > 0 ? 'success' : (props.average === 0 ? 'light' : 'danger'));

function fetchDayData (days: number) {
    getDayMosaicData(days)
        .then(data => {
            daysData.value = data;
        });
}
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>

<style scoped>

</style>
