<template>
    <div>
        <h3 class="text-2xl font-bold mb-2">
            <category-label :category="category"/>
            <score-badge :value="category.statistics.average" />
        </h3>
        <mosaic class="w-full" style="height: 10em" :data="data" @change-resolution="fetchData" />
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { CategoryFull } from '@interfaces';
import { getCategoryMosaicData } from '@requests/mosaic';
import Mosaic from './Mosaic.vue';
import CategoryLabel from '@components/categories/CategoryLabel.vue';
import ScoreBadge from '@tools/ScoreBadge.vue';

interface Props {
    category: CategoryFull
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
