<template>
  <div class="loading-container">
    <span class="loading loading-spinner" role="status"></span>
  </div>
</template>

<script setup lang="ts">

</script>

<style scoped lang="scss">
.loading-container {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  background: white;
  opacity: 0.8;
  z-index: 1001;

  .loading-spinner {
    position: absolute;
    top: 50%;
    left: 50%;
  }
}
</style>
