<template>
    <div class="w-100 border-bottom border-1 py-2 user-select-none m-0 text-center bg-white">
        {{ level.text }}
        <small v-if="level.showDiff && statistics.average > score" class="text-dark-emphasis">{{ (statistics.average - score).toFixed(1) }} more to beat average</small>
        <small v-else-if="level.showDiff" class="text-dark-emphasis">{{ (score - statistics.average).toFixed(1) }} over average</small>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Statistics } from '@interfaces';

interface Props {
    score: number
    statistics: Statistics
}

const props = defineProps<Props>();

interface Level {
    minScore: number
    text: string
    showDiff?: boolean
}

const LEVELS: Level[] = [
    { minScore: 3, text: '🏆 Unbelievable !!!!', showDiff: true },
    { minScore: 2, text: '🎖️ What a day !!!', showDiff: true },
    { minScore: 1.5, text: '🏅 You crushed it !!', showDiff: true },
    { minScore: 1, text: '🎉 You did it !', showDiff: true },
    { minScore: 0.75, text: '🏃 Almost there...', showDiff: true },
    { minScore: 0.001, text: '🚶 Good job !' },
    { minScore: 0, text: '🧍 Let\'s go' },
    { minScore: -Infinity, text: '🧎 You\'ll do better' }
];

const level = computed<Level>(() => LEVELS.filter(l => props.score >= l.minScore * props.statistics.average)[0]);
</script>
