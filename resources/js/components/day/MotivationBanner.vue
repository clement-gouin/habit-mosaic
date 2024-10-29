<template>
    <div class="w-full border-b border-1 py-2 select-none m-0 text-center bg-white">
        <span v-if="loading">
            Loading...
        </span>
        <span v-else-if="statistics.total < 7">
            We need more data, keep going !
        </span>
        <span v-else-if="isToday">
            {{ level.text }}
            <small v-if="level.showDiff && statistics.average > score" class="text-gray-600">{{ (statistics.average - score).toFixed(1) }} more to beat average</small>
            <small v-else-if="level.showDiff" class="text-gray-600">{{ (score - statistics.average).toFixed(1) }} over average</small>
        </span>
        <span v-else>
            {{ level.otherText }}
            <small v-if="statistics.average > score" class="text-gray-600">{{ (statistics.average - score).toFixed(1) }} under average</small>
            <small v-else class="text-gray-600">{{ (score - statistics.average).toFixed(1) }} over average</small>
        </span>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Statistics } from '@interfaces';

interface Props {
    score: number
    statistics: Statistics
    loading?: boolean
    isToday?: boolean
}

const props = defineProps<Props>();

interface Level {
    minScore: number
    text: string
    otherText?: string
    showDiff?: boolean
}

const levels = computed<Level[]>(() => [
    { minScore: props.statistics.max, text: '🏆 Unbelievable !!!!', showDiff: true, otherText: '🏆 Top day' },
    { minScore: props.statistics.upper_quartile * 0.5 + props.statistics.max * 0.5, text: '🎖️ What a day !!!', showDiff: true, otherText: '🎖️ One of the best days' },
    { minScore: props.statistics.upper_quartile, text: '🏅 You crushed it !!', showDiff: true, otherText: '🏅 Better than 75% of days' },
    { minScore: props.statistics.average, text: '🎉 You did it !', showDiff: true, otherText: '🎉' },
    { minScore: props.statistics.average * 0.5 + props.statistics.lower_quartile * 0.5, text: '🏃 Almost there...', showDiff: true, otherText: '🚶 Better than 25% of days' },
    { minScore: props.statistics.lower_quartile, text: '🚶 Better than 25% of days', otherText: '🚶 Better than 25% of days' },
    { minScore: props.statistics.lower_quartile * 0.5, text: '🚶 Good job !', otherText: '🚶' },
    { minScore: 0, text: '🧍 Let\'s go', otherText: '🧍' },
    { minScore: -Infinity, text: '🧎 You\'ll do better', otherText: '🧎' }
]);

const level = computed<Level>(() => levels.value.filter(l => props.score >= l.minScore)[0]);
</script>
