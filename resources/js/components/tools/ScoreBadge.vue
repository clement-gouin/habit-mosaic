<template>
    <small :title="title"
        class="superscript fs-6 rounded border p-1 ms-1"
        :style="{backgroundColor: color('bg-subtle'), borderColor: color('border-subtle'), color: color('text-emphasis')}"
    >
        <span v-if="additionalValue !== undefined">{{ value.toFixed(precision) }} | {{ additionalValue.toFixed(additionalPrecision) }}</span>
        <span v-else>{{ value.toFixed(precision) }}</span>
    </small>
</template>

<script setup lang="ts">
import { referenceColor } from '@utils/colors';

interface Props {
    value: number
    reference?: number
    title?: string
    precision?: number
    additionalValue?: number
    additionalPrecision?: number
}

const props = withDefaults(defineProps<Props>(), { title: 'average score', reference: 1, precision: 1, additionalPrecision: 1 });

const color = (variable: string) => referenceColor(props.value, props.reference, variable);
</script>
