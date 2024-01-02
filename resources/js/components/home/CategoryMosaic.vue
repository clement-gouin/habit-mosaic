<template>
    <div>
        <h3><category-label :category="category"/></h3>
        <mosaic class="w-100" style="height: 10em" :data="data" @change-resolution="fetchData" />
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Category } from '@interfaces';
import { getCategoryMosaicData } from '@requests/mosaic';
import Mosaic from './Mosaic.vue';
import CategoryLabel from '../categories/CategoryLabel.vue';

interface Props {
    category: Category
}

const props = defineProps<Props>();

const data = ref<(number|null)[]>([]);

function fetchData (days: number) {
    getCategoryMosaicData(props.category, days)
        .then(d => {
            data.value = d;
        });
}

</script>

<style scoped>

</style>
