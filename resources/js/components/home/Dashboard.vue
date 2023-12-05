<template>
    <h1>Dashboard</h1>
    <div class="d-flex flex-row flex-wrap">
        <template v-for="(tracker,i) in trackers" v-bind:key="tracker.id">
            <tracker-input class="me-1 me-lg-2 mb-1 mb-lg-2" v-model="trackers[i]" />
        </template>
    </div>
</template>

<script setup lang="ts">
import { Tracker } from '@interfaces';
import TrackerInput from '@tools/TrackerInput.vue';
import { ref } from 'vue';

interface Props {
    trackers: Tracker[]
}

const props = defineProps<Props>();

const trackers = ref<Tracker[]>(props.trackers);
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>

<style scoped>

</style>
