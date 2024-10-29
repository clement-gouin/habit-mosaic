<template>
    <div class="form-control my-2">
        <label :for="id" v-if="label" class="label pt-0">
            <span class="label-text" :class="hasError ? 'text-error' : ''">{{ label }}<span v-if="required" class="text-error">*</span></span>
        </label>
        <select
            v-model="internalValue"
            :id="id"
            :name="name ?? id"
            :disabled="disabled"
            :required="required"
            class="select select-bordered"
            :class="`select-${displayColor} ` + (hasError ? 'input-error text-error' : '')"
            @blur="onBlur"
        >
            <option v-if="placeholder" disabled value="">{{ placeholder }}</option>
            <option v-if="!required" value="">{{ emptyText }}</option>
            <option v-for="option in options" v-bind:key="option.key" :value="option.key">
                <slot :option="option">{{ option.label ?? option.value }}</slot>
            </option>
        </select>
        <div v-if="helpText ?? (typeof displayError === 'string' && displayError.length)" class="label pb-0">
            <span class="label-text-alt" :class="hasError ? 'font-bold text-error' : 'italic'">{{
              (typeof displayError === 'string' && displayError.length) ? displayError : helpText
            }}</span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { BaseFormInput, Option } from '@interfaces';

interface Props extends BaseFormInput {
    options: Option<unknown>[]
    emptyText?: string
}

const props = withDefaults(defineProps<Props>(), { emptyText: '(none)' });

const emit = defineEmits(['change']);

const value = defineModel<Option<unknown>|null>();
const internalError = ref<string>();
const internalValue = ref<string|null>(value.value?.key ?? '');

const hasError = computed<boolean>(() => !!props.error || !!internalError.value || false);
const displayError = computed<undefined|string|boolean>(() => (typeof props.error === 'string' && props.error.length) ? props.error : internalError.value);
const displayColor = computed<undefined|string>(() => hasError.value ? 'error' : props.color);

function updateInternal () {
    value.value = props.options.find((option: Option<unknown>) => option.key === internalValue.value);
    emit('change', value.value);
}

watch(internalValue, updateInternal);

function onBlur () {
    updateInternal();
    internalError.value = '';
    if (props.required && value.value?.length === 0) {
        internalError.value = 'This field is required';
    }
}
</script>
