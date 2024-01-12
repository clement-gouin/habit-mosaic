<template>
    <div class="w-100 border-bottom border-1 py-2 user-select-none m-0 text-center bg-white">
        <span v-if="score < 0">🧎 You'll do better </span>
        <span v-else-if="score === 0">🧍 Let's go !</span>
        <span v-else-if="score < average * 0.75">🚶 Good job ! </span>
        <span v-else-if="score < average">🏃 Almost there... <small class="text-dark-emphasis">{{ (average - score).toFixed(1) }} more to beat average</small></span>
        <span v-else-if="score < average * 1.5">🎉 You did it ! <small class="text-dark-emphasis">{{ (score - average).toFixed(1) }} over average</small></span>
        <span v-else>🏅 You crushed it !! <small class="text-dark-emphasis">{{ (score - average).toFixed(1) }} over average</small></span>
    </div>
</template>

<script setup lang="ts">
interface Props {
    score: number
    average: number
}

defineProps<Props>();
</script>

<style scoped>

</style>
