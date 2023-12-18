<template>
    <div class="alerts-container">
        <alert
            v-for="alert in alerts"
            v-bind:key="alert.id"
            :alert="alert"
        />
    </div>
</template>

<script setup lang="ts">
import { useAlertsStore } from '@stores/alerts';
import Alert from './Alert.vue';

const { alerts } = useAlertsStore();
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>

<style scoped>
.alerts-container {
    position: fixed;
    z-index: 1000;
    width: 30%;
    top: 0;
    right: 0;
    padding-top: 1em;
    padding-right: 1em;
}
</style>
