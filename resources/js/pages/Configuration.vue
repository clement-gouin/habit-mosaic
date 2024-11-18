<template>
    <div class="p-1 py-2 sm:p-2 md:p-4">
        <h1 class="border-b pb-2 mb-4 text-3xl font-bold"><i class="fa-solid fa-wrench" />&nbsp;Configuration</h1>
        <div class="w-full grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="col-span-2">
                <h2 class="text-2xl font-bold mb-2">Trackers</h2>
                <tracker-list v-model="trackers" />
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-2">Categories</h2>
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
