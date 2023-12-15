<template>
    <div class="alert-container">
        <div
            v-for="notification in notifications"
            v-bind:key="notification.id"
            :class="`alert-${notification.type} ${notification.show ? 'show' : ''}`"
            class="alert alert-dismissible fade"
            role="alert"
        >
            <h4 v-if="notification.title.length > 0" class="alert-heading">{{ notification.title }}</h4>
            <div>{{ notification.text }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useNotificationsStore } from '@stores/notifications';

const { notifications } = useNotificationsStore();
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>

<style scoped>
.alert-container {
    position: fixed;
    z-index: 1000;
    width: 30%;
    top: 0;
    right: 0;
    padding-top: 1em;
    padding-right: 1em;
}

.alert-container .alert {
    margin-bottom: 0;
    margin-top: 18px;
}

.alert-container {
    position: fixed;
    z-index: 1000;
    width: 30%;
    top: 0;
    right: 0;
    padding-top: 1em;
    padding-right: 1em;
}

.alert-container .alert {
    margin-bottom: 0;
    margin-top: 18px;
}
</style>
