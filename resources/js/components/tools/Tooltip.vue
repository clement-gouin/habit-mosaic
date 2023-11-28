<template>
  <div class="tooltip-container">
    <div @mouseenter="visible = tooltipIsNotEmpty" @mouseleave="visible = false"><slot></slot></div>
    <div role="tooltip" class="fade tooltip bottom-left in" v-show="visible">
      <div class="tooltip-arrow"></div>
      <div class="tooltip-inner" v-html="text"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';

interface Props {
    text?: string
}

const props = defineProps<Props>();

const visible = ref(false);

const tooltipIsNotEmpty = computed(() => props.text != null && props.text.length > 0);
</script>

<style scoped>
.tooltip-container {
  display: inline-block;
  position: relative;
}

.tooltip.bottom-left {
  display:inline-block;
  right: 0;
  top: 125%
}
</style>
