<template>
    <div
        :class="`alert-${alert.type} ${alert.show ? 'show' : ''}`"
        class="alert alert-dismissible fade"
        role="alert"
    >
        <h4 v-if="alert.title" class="alert-heading">{{ alert.title }}</h4>
        <div>{{ alert.text }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</template>

<script setup lang="ts">
import { Alert } from '@interfaces';

interface Props {
    alert: Alert
}

defineProps<Props>();
</script>

<style scoped>
.alert {
    margin-bottom: 0;
    margin-top: 18px;
}
</style>
