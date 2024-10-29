<template>
    <div class="fixed bottom-0 right-0 gap-2 mr-4 z-50" style="z-index: 1003;">
        <template v-for="alert in alerts" v-bind:key="alert.id">
            <Alert :alert="alert" />
        </template>
    </div>
</template>

<script setup lang="ts">
import { useAlertsStore } from '@stores/alerts';
import Alert from '@components/alerts/Alert.vue';

const { alerts } = useAlertsStore();
</script>
