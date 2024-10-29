<template>
    <div class="form-control text-base my-2">
        <label :for="id" v-if="label" class="label pt-0">
            <span class="label-text" :class="hasError ? 'text-error' : ''">{{ label }}<span v-if="required" class="text-error">*</span></span>
        </label>
        <textarea
            v-model="value"
            :id="id"
            :name="name ?? id"
            :placeholder="placeholder"
            :disabled="disabled"
            :required="required"
            :readonly="readonly"
            class="textarea textarea-bordered"
            :class="`textarea-${displayColor} ` + (hasError ? 'input-error text-error' : '')"
            @blur="onBlur"
        />
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

interface Props extends BaseFormInput {
    placeholder?: string
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
    }

    emit('change', value.value);
}
</script>
