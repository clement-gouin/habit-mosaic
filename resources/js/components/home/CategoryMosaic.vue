<template>
    <div>
        <h3>
            <category-label :category="category"/>
            <small class="superscript fs-6 text-dark-emphasis rounded border p-1 ms-1" :style="{backgroundColor: color('bg-subtle'), borderColor: color('border-subtle'), color: color('text-emphasis')}">{{ category.average.toFixed(1) }}</small>
        </h3>
        <mosaic class="w-100" style="height: 10em" :data="data" @change-resolution="fetchData" />
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { CategoryFull } from '@interfaces';
import { getCategoryMosaicData } from '@requests/mosaic';
import Mosaic from './Mosaic.vue';
import CategoryLabel from '../categories/CategoryLabel.vue';
import { referenceColor } from '@utils/colors';

interface Props {
    category: CategoryFull
}

const props = defineProps<Props>();

const data = ref<(number|null)[]>([]);
const max = computed<number>(() => Math.max(...data.value.map(d => Math.abs(d ?? 0))));
const color = (variable: string) => referenceColor(props.category.average, max.value, variable);

function fetchData (days: number) {
    getCategoryMosaicData(props.category, days)
        .then(d => {
            data.value = d;
        });
}

</script>

<style scoped>

</style>
