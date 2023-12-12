<template>
  <modal :title="title"
         can-close
         :action-text="confirmButtonText"
         :close-text="cancelButtonText"
         :action-color="confirmButtonColor"
         ref="dialog"
         @submit="confirm"
         @close="cancel"
         :auto-close="false"
         :no-body="!body"
  >
    <template #modal-header v-if="!!$slots['header']">
      <slot name="header"></slot>
    </template>
    <slot name="body">
      <p>{{ body }}</p>
    </slot>
  </modal>
</template>

<script setup lang="ts">

import Modal from '@tools/Modal.vue';
import { ref } from 'vue';

interface Props {
    title?: string,
    body?: string,
    confirmButtonText?: string,
    confirmButtonColor?: string,
    cancelButtonText?: string,
    confirmErrorMessage? : string,
}

defineProps<Props>();

const dialog = ref<InstanceType<Modal> | null>(null);

let resolvePromise: (value: (boolean | PromiseLike<boolean>)) => void;

function show () {
    dialog.value.open();
    return new Promise<boolean>(resolve => {
        resolvePromise = resolve;
    });
}

function hide () {
    dialog.value.close();
}

function confirm () {
    hide();
    resolvePromise(true);
}

function cancel () {
    hide();
    resolvePromise(false);
}

defineExpose({ show, hide });
</script>

<style scoped>

</style>
