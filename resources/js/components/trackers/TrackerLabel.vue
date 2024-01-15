<template>
    <i :class="mapToClassName(tracker.icon)"></i>
    {{ tracker.name }}
</template>

<script setup lang="ts">
import { mapToClassName } from '@utils/icons';
import { Tracker } from '@interfaces';

interface Props {
    tracker: Tracker,
}

defineProps<Props>();
</script>
