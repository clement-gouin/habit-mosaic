<template>
    <i v-if="category.icon" :class="mapToClassName(category.icon)"></i>
    {{ category.name }}
</template>

<script setup lang="ts">
import { mapToClassName } from '@utils/icons';
import { Category } from '@interfaces';

interface Props {
    category: Category,
}

defineProps<Props>();
</script>

<style scoped>

</style>
