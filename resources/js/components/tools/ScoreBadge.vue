<template>
    <small :title="title"
        class="superscript font-normal rounded border p-1 ms-1"
        :style="{backgroundColor: backgroundColor(baseColor), borderColor: borderColor(baseColor), color: textColor(baseColor)}"
    >
        <span v-if="additionalValue !== undefined">{{ value.toFixed(precision) }} | {{ additionalValue.toFixed(additionalPrecision) }}</span>
        <span v-else>{{ value.toFixed(precision) }}</span>
    </small>
</template>

<script setup lang="ts">
import { backgroundColor, borderColor, referenceColor, textColor } from '@utils/colors';
import { computed } from 'vue';

interface Props {
    value: number
    reference?: number
    title?: string
    precision?: number
    additionalValue?: number
    additionalPrecision?: number
}

const props = withDefaults(defineProps<Props>(), { title: 'average score', reference: 1, precision: 1, additionalPrecision: 1 });

const baseColor = computed(() => referenceColor(props.value, props.reference));
</script>
