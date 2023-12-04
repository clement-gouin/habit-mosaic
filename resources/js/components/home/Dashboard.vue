<template>
    <h1>Dashboard</h1>
    <div class="row position-relative">
        <p class="col-6">
            <bs-form horizontal>
                <icon-input name="test" v-model="icon" />
            </bs-form>
        </p>
    </div>
</template>

<script setup lang="ts">

import IconInput from '@tools/forms/IconInput.vue';
import BsForm from '@tools/forms/BsForm.vue';
</script>

<script lang="ts">
import { ref } from 'vue';

export default { inheritAttrs: false };

const icon = ref<string|null>(null);
</script>

<style scoped>

</style>
