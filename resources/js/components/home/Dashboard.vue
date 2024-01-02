<template>
    <div class="p-1 p-sm-2 p-md-4">
        <h1 class="border-bottom border-1 pb-2">Dashboard</h1>
        <mosaic class="w-100" style="height: 10em" :data="daysData" @change-resolution="fetchDayData" />
        <hr>
        <div class="row">
            <category-mosaic v-for="category in categories" v-bind:key="category.id" class="col-12 col-md-6" :category="category" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { Category, TrackerFull } from '@interfaces';
import Mosaic from './Mosaic.vue';
import { ref } from 'vue';
import { getDayMosaicData } from '@requests/mosaic';
import CategoryMosaic from './CategoryMosaic.vue';

interface Props {
    categories: Category[],
    trackers: TrackerFull[],
}

defineProps<Props>();

const daysData = ref<(number|null)[]>([]);

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
