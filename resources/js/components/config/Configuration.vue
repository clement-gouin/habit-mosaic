<template>

</template>

<script setup lang="ts">
import {Category, Tracker} from "@interfaces";
import {ref} from "vue";

interface Props {
    categories: Category[],
    trackers: Tracker[]
}

const props = defineProps<Props>();

const categories = ref<Category[]>(props.categories);
const trackers = ref<Tracker[]>(props.trackers);
</script>

<style scoped>

</style>
