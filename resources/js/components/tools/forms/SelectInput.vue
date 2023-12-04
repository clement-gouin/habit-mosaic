<template>
    <div class="form-group" :class="{'has-error': (error || emptyError), 'row': isHorizontal}">
        <label :class="labelClass" :for="name" v-if="label">
            <template v-if="helpText">
                <Tooltip :text="helpText">{{ label }}<span v-if="required" class="text-danger">*</span>&nbsp;
                    <span class="badge">?</span></Tooltip>
            </template>
            <template v-else>
                {{ label }}<span v-if="required" class="text-danger">*</span>
            </template>
        </label>
        <div :class="inputWrapperClass">
            <select
                ref="input"
                :name="name"
                class="form-control"
                :value="findOption(modelValue)?.key"
                :disabled="disabled"
                :aria-describedby="'help-' + name"
                :required="required"
                @blur="onBlur"
                @focus="onFocus"
                @change="onChange"
            >
                <option v-for="option in options" :value="option.key" :key="option.key" :disabled="option.disabled">
                    {{ option.label }}
                </option>
            </select>
            <span v-if="error" :id="'help-' + name" class="form-text">{{ error }}</span>
            <span v-else-if="emptyError" :id="'help-' + name" class="form-text">This field is required</span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onDeactivated, ref } from 'vue';
import Tooltip from '@tools/Tooltip.vue';
import { Option } from '@interfaces';
import { useBsForm } from '@composables/useBsForm';

interface Props {
    /** name of field in form */
    name: string,
    /** initial value */
    modelValue?: string | number | Option,
    /** label associated with field */
    label?: string,
    /** tooltip on label hover */
    helpText?: string,
    /** current error of field */
    error?: string,
    /** field is disabled */
    disabled?: boolean,
    /** field is required */
    required?: boolean,
    readonly?: boolean,
    /** values available in field */
    options: Option[],
    labelColSize?: number,
    inputWrapperColSize?: number,
    emitOption?: boolean,
    emitValue?: boolean,
}

const props = defineProps<Props>();

const input = ref<HTMLInputElement | null>(null);
const emptyError = ref(false);

const emit = defineEmits(['update:modelValue', 'lazyLoad']);

const { labelClass, inputWrapperClass, isHorizontal } = useBsForm(props);

function findOption (value: string | number | Option | undefined): Option | undefined {
    if (!value) {
        return undefined;
    }

    if (props.emitOption) {
        return value as Option;
    }

    if (props.emitValue) {
        return props.options.find((option: Option) => '' + option.value === '' + value);
    }

    return props.options.find((option: Option) => '' + option.key === '' + value);
}

function onBlur () {
    emptyError.value = (props.required as boolean) && (input.value?.value === '');
}

function onFocus () {
    emit('lazyLoad');
}

function onChange () {
    const selected = props.options.find((option: Option) => '' + option.key === input.value?.value);

    if (props.emitOption) {
        emit('update:modelValue', selected);
        return;
    }

    if (props.emitValue) {
        emit('update:modelValue', selected?.value);
        return;
    }

    emit('update:modelValue', selected?.key);
}

onDeactivated(() => {
    emptyError.value = false;
});
</script>
