<template>
  <form :class="{'form-horizontal': isHorizontal, 'form-inline': isInline}" @submit.prevent="$emit('submit')">
    <slot/>
  </form>
</template>

<script setup lang="ts">

import { computed, provide, toRef } from 'vue';
import { BsFormKey } from '@symbols';

interface Props {
    vertical?: boolean;
    horizontal?: boolean;
    floating?: boolean;
    inline?: boolean;
    labelColSize?: number;
    inputWrapperColSize?: number;
}

const props = withDefaults(defineProps<Props>(), {
    horizontal: false,
    vertical: false,
    inline: false
});

const isHorizontal = computed<boolean>(() => props.horizontal && !props.vertical && !props.inline);
const isInline = computed<boolean>(() => props.inline && !props.horizontal && !props.vertical);

defineEmits(['submit']);

provide(BsFormKey, {
    labelColSize: toRef(props, 'labelColSize'),
    inputWrapperColSize: toRef(props, 'inputWrapperColSize'),
    isHorizontal,
    isInline,
    isFloating: toRef(props, 'floating')
});
</script>

<style scoped lang="scss">

</style>
