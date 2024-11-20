<template>
    <div role="alert" class="alert shadow transition-opacity ease-in-out mb-4 w-fit ml-auto" :class="alert.fade ? 'opacity-0 duration-700' : 'opacity-100 duration-200'">
        <i class="fa-lg" :class="`text-${alert.type} ${icon}`"/>
        <span v-if="alert.title" class="font-bold inline-block mr-2" v-html="alert.title + ':'"></span>
        <span v-html="alert.text" />
    </div>
</template>

<script setup lang="ts">
import { Alert, AlertType } from '@interfaces';
import { computed } from 'vue';

interface Props {
    alert: Alert
}

const props = defineProps<Props>();

const icon = computed<string>(() => {
    switch (props.alert.type) {
        case AlertType.Info:
            return 'fas fa-circle-info';
        case AlertType.Success:
            return 'fas fa-circle-check';
        case AlertType.Warning:
            return 'fas fa-circle-exclamation';
        default:
            return 'fas fa-circle-xmark';
    }
});
</script>
