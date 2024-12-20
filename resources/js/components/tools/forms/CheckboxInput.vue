<template>
    <div class="form-control my-2">
        <label :for="id" class="label cursor-pointer py-0">
            <span v-if="label" class="label-text mr-1 flex-grow" :class="{'text-error': hasError, 'text-right': labelAfter}">
              {{ label }}
              <span v-if="required" class="text-error">*</span>
            </span>
            <input
                type="checkbox"
                v-model="value"
                :id="id"
                :name="name ?? id"
                :disabled="disabled"
                :required="required"
                :readonly="readonly"
                :class="`${displayType} ${displayType}-${displayColor} ` + (hasError ? 'input-error' : '')"
            />
            <span v-if="labelAfter" class="label-text ml-1 flex-grow" :class="{'text-error': hasError}">
              {{ labelAfter }}
            </span>
        </label>
        <div v-if="helpText ?? (typeof displayError === 'string' && displayError.length)" class="label -mt-1 pb-0">
            <span class="label-text-alt" :class="hasError ? 'font-bold text-error' : 'italic'">{{
              (typeof displayError === 'string' && displayError.length) ? displayError : helpText
            }}</span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { BaseFormInput } from '@interfaces';

interface Props extends BaseFormInput {
    toggle?: boolean
    labelAfter?: string
}

const props = defineProps<Props>();

const emit = defineEmits(['change']);

const value = defineModel<boolean>();
const internalError = ref<string>();

const hasError = computed<boolean>(() => !!props.error || !!internalError.value || false);
const displayType = computed<string>(() => props.toggle ? 'toggle' : 'checkbox');
const displayError = computed<undefined|string|boolean>(() => (typeof props.error === 'string' && props.error.length) ? props.error : internalError.value);
const displayColor = computed<undefined|string>(() => hasError.value ? 'error' : (props.color ?? 'primary'));

watch(value, () => {
    emit('change', value.value);
    internalError.value = '';
    if (props.required && !value.value) {
        internalError.value = 'This field is required';
    }
});
</script>
