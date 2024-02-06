<template>
  <div class="loading">
    <div class="spinner-border" role="status"></div>
  </div>
</template>

<script setup lang="ts">

</script>

<style scoped lang="scss">
.loading {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  background: white;
  opacity: 0.8;
  z-index: 1001;

  .spinner-border {
    position: absolute;
    top: 50%;
    left: 50%;
  }
}
</style>
