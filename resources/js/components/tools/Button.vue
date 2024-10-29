<template>
    <a :href="href ?? '#'" role="button" @click="click" class="btn" :class="`btn-${type} ${sizeClass} ${disabled ? 'disabled' : ''}`"><slot /></a>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    type?: string
    href?: string
    small?: boolean
    large?: boolean
    disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), { type: 'primary' });

const emit = defineEmits(['click']);

const sizeClass = computed(() => (props.small ? 'btn-sm' : (props.large ? 'btn-lg' : '')));

function click (event: Event) {
    if (!props.href) {
        event?.preventDefault();
        emit('click');
    }
}
</script>
