<template>
    <div class="form-control text-base my-2">
        <label :for="id" v-if="label" class="label pb-0">
            <span class="label-text" :class="hasError ? 'text-error' : ''">{{ label }}<span v-if="required" class="text-error">*</span></span>
        </label>
        <label
            :for="id"
            class="input input-bordered flex items-center gap-2"
            :class="`input-${displayColor} ` + (hasError ? 'text-error' : '')"
        >
            <slot name="left">
                <span class="text-lg"><i v-if="icon" class="w-5 h-5 opacity-70 fas" :class="`fa-${icon}`" /></span>
                <span v-if="inLabel" class="label-text" :class="{'text-error': hasError}">
                    {{ inLabel }}<span v-if="required" class="text-error">*</span>
                </span>
            </slot>
            <input
                :type="type"
                v-model="value"
                :id="id"
                :name="name ?? id"
                :placeholder="placeholder"
                :disabled="disabled"
                :required="required"
                :readonly="readonly"
                class="grow"
                @blur="onBlur"
            />
            <slot name="right"></slot>
        </label>
        <div v-if="helpText ?? (typeof displayError === 'string' && displayError.length)" class="label pb-0">
      <span class="label-text-alt" :class="hasError ? 'font-bold text-error' : 'italic'">{{
              (typeof displayError === 'string' && displayError.length) ? displayError : helpText
          }}</span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { BaseFormInput } from '@interfaces';
import { validateEmail } from '@utils/forms';

interface Props extends BaseFormInput {
    inLabel?: string
    placeholder?: string
    icon?: string
    type?: string
}

const props = defineProps<Props>();

const emit = defineEmits(['change']);

const value = defineModel<string>();
const internalError = ref<string>();

const hasError = computed<boolean>(() => !!props.error || !!internalError.value || false);
const displayError = computed<undefined|string|boolean>(() => (typeof props.error === 'string' && props.error.length) ? props.error : internalError.value);
const displayColor = computed<undefined|string>(() => hasError.value ? 'error' : props.color);

function onBlur () {
    internalError.value = '';
    if (props.required && value.value?.length === 0) {
        internalError.value = 'This field is required';
    } else if (value.value?.length) {
        let newValue: string | null = value.value.trim();
        switch (props.type) {
            case 'email':
                newValue = validateEmail(value.value);
                if (!newValue) {
                    internalError.value = 'Invalid email format';
                }
                break;
        }
        if (newValue !== null && newValue !== value.value) {
            value.value = newValue;
        }
    }

    emit('change', value.value);
}
</script>
