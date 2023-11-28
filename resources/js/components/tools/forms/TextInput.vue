<template>
    <div class="form-group" :class="(error || fieldError) ? 'has-error' : ''">
        <label v-if="!isFloating" :class="labelClass" :for="name">
            <template v-if="helpText">
                <Tooltip :text="helpText">{{ label }}<span v-if="required" class="text-danger">*</span>&nbsp;<span
                    class="badge">?</span></Tooltip>
            </template>
            <template v-else>
                {{ label }}<span v-if="required" class="text-danger">*</span>
            </template>
        </label>
        <component :class="inputWrapperClass" :is="isHorizontal ? 'div' : 'template'">
            <input
                ref="input"
                :name="name"
                :type="type"
                class="form-control"
                :value="modelValue"
                :disabled="disabled"
                :aria-describedby="'help-' + name"
                :required="required"
                :placeholder="placeholder"
                :autocomplete="autocomplete"
                :readonly="readonly"
                :title="error ?? fieldError ?? notice"
                @input="onInput"
                @blur="onBlur"
                @change="onChange"
            >
            <span v-if="!hideHelp">
                <span v-if="error" :id="'help-' + name" class="help-block">{{ error }}</span>
                <span v-else-if="fieldError" :id="'help-' + name" class="help-block">{{ fieldError }}</span>
                <span v-else-if="notice || $slots.notice" class="help-block"><slot name="notice">{{ notice }}</slot></span>
            </span>
            <label v-if="isFloating" :for="name">{{ label }}<span v-if="required" class="text-danger">*</span></label>
        </component>
    </div>
</template>

<script setup lang="ts">
import { onDeactivated, ref } from 'vue';
import Tooltip from '@tools/Tooltip.vue';
import { validateEmail } from '@utils/forms';
import { useBsForm } from '@composables/useBsForm';

interface Props {
    name: string,
    modelValue?: string,
    type?: string,
    placeholder?: string,
    label?: string,
    helpText?: string,
    error?: string,
    disabled?: boolean,
    required?: boolean,
    autocomplete?: string,
    readonly?: boolean,
    labelColSize?: number,
    inputWrapperColSize?: number,
    notice?: string,
    hideHelp?: boolean,
}

const props = withDefaults(defineProps<Props>(), { type: 'text' });

const emit = defineEmits(['update:modelValue', 'change']);

const input = ref<HTMLInputElement | null>(null);
const fieldError = ref('');

const { labelClass, inputWrapperClass, isHorizontal, isFloating } = useBsForm(props);

function onBlur (event: InputEvent) {
    const inputField = event.currentTarget as HTMLInputElement;
    fieldError.value = '';
    if ((props.required as boolean) && (inputField.value === '')) {
        fieldError.value = 'This field is required';
    } else if (inputField.value !== '') {
        let newValue: string | null = inputField.value.trim();
        switch (props.type) {
            case 'email':
                newValue = validateEmail(inputField.value);
                if (!newValue) {
                    fieldError.value = 'Invalid email';
                }
                break;
        }
        if (newValue !== null && newValue !== inputField.value) {
            inputField.value = newValue;
            emit('update:modelValue', newValue);
            emit('change', newValue);
        }
    }
}

function onInput (event: InputEvent) {
    emit('update:modelValue', (event.currentTarget as HTMLInputElement).value);
}

function onChange (event: InputEvent) {
    emit('change', (event.currentTarget as HTMLInputElement).value);
}

onDeactivated(() => {
    fieldError.value = '';
});
</script>
