<template>
    <div class="p-1 p-sm-2 p-md-4">
        <h1 class="border-bottom border-1 pb-2">Configuration</h1>
        <div class="row">
            <div class="col-12 col-xl-9">
                <h2>Trackers</h2>
                <tracker-list v-model="trackers" />
            </div>
            <div class="col-12 col-xl-3">
                <h2>Categories</h2>
                <category-list v-model="categories" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Category, Tracker } from '@interfaces';
import { ref } from 'vue';
import CategoryList from '@components/categories/CategoryList.vue';
import TrackerList from '@components/trackers/TrackerList.vue';

interface Props {
    categories: Category[],
    trackers: Tracker[]
}

const props = defineProps<Props>();

const categories = ref<Category[]>(props.categories);
const trackers = ref<Tracker[]>(props.trackers);
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>
