<template>
  <div class="tooltip" data-tip="text">
      <slot></slot>
  </div>
</template>

<script setup lang="ts">
interface Props {
    text?: string
}

defineProps<Props>();
</script>
