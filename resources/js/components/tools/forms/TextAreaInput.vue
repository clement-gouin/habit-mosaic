<template>
    <div class="form-group" :class="{'has-error': (error || internalError)}">
        <label :class="labelClass" :for="name" v-if="label">
            <template v-if="helpText">
                <Tooltip :text="helpText">{{ label }}<span v-if="required" class="text-danger">*</span>&nbsp;<span
                    class="badge">?</span></Tooltip>
            </template>
            <template v-else>
                {{ label }}<span v-if="required" class="text-danger">*</span>
            </template>
        </label>
        <component :class="inputWrapperClass" :is="isHorizontal ? 'div' : 'template'">
      <textarea
          :name="name"
          :rows="rows"
          class="form-control"
          :value="modelValue"
          :disabled="disabled"
          :aria-describedby="'help-' + name"
          :required="required"
          :placeholder="placeholder"
          :autocomplete="autocomplete"
          :readonly="readonly"
          v-bind="$attrs"
          @change="onChange"
          @input="onInput"
      ></textarea>
            <span v-if="internalError" :id="`help-${name}`" class="help-block">{{ internalError }}</span>
            <span v-else-if="error" :id="`help-${name}`" class="help-block">{{ error }}</span>
            <span v-else-if="notice || $slots.notice" :id="`help-${name}`" class="help-block"><slot name="notice">{{ notice }}</slot></span>
        </component>
    </div>
</template>
<script setup lang="ts">
import { onDeactivated, ref } from 'vue';
import Tooltip from '@tools/Tooltip.vue';
import { useBsForm } from '@composables/useBsForm';

interface Props {
    name: string,
    modelValue?: string,
    placeholder?: string,
    label?: string,
    helpText?: string,
    notice?: string,
    error?: string,
    disabled?: boolean,
    required?: boolean,
    autocomplete?: string,
    readonly?: boolean,
    labelColSize?: number,
    inputWrapperColSize?: number,
    rows?: number
}

const props = defineProps<Props>();

const emit = defineEmits(['update:modelValue', 'change']);

const internalError = ref<string | null>(null);

const { labelClass, inputWrapperClass, isHorizontal } = useBsForm(props);

function onInput (event: InputEvent) {
    const value = (event.currentTarget as HTMLInputElement).value;
    emit('update:modelValue', value);
    internalError.value = null;
    if ((props.required as boolean) && (value === '')) {
        internalError.value = 'This field is required';
    }
}

function onChange (event: InputEvent) {
    const target = (event.currentTarget as HTMLInputElement);
    emit('change', target.value);
}

onDeactivated(() => {
    internalError.value = null;
});
</script>
<script lang="ts">
export default { inheritAttrs: false };
</script>
