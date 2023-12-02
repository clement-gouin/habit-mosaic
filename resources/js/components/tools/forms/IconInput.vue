<template>
    <dropdown-input
        ref="input"
        :name="name"
        v-model="icon"
        :placeholder="placeholder"
        :label="label"
        :error="error"
        :disabled="disabled"
        :required="required"
        :options="options"
        @search="onSearch"
        :help-text="helpText"
        :readonly="readonly"
        :debounce="500"
        with-highlight
        @change="onChange"
        :notice="notice"
    >
        <template #item="option">
            <i :class="mapToClassName(option.value)"></i> {{ option.value }}
        </template>
    </dropdown-input>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Option } from '@interfaces';
import {getIconList, searchIcon, mapToClassName} from "@utils/icons";
import DropdownInput from "@tools/forms/DropdownInput.vue";

interface Props{
    name: string,
    modelValue: string | null,
    placeholder?: string,
    label?: string,
    helpText?: string,
    error?: string,
    disabled?: boolean,
    required?: boolean,
    readonly?: boolean,
    notice?: string,
}

function iconNameToOption(name: string): Option {
    return { key: name, label: name, value: name };
}

const icon = computed<Option|null>({
    get (): Option | null {
        return props.modelValue == null ? null : iconNameToOption(props.modelValue);
    },
    set (selected: Option | null) {
        emit('update:modelValue', selected?.value);
    }
});

const props = defineProps<Props>();
const emit = defineEmits(['update:modelValue', 'change']);
const options = ref<Option[]>(getIconList().map(iconNameToOption));

async function onSearch (value = '') {
    options.value = searchIcon(value).map(iconNameToOption);
}

function onChange (value: Option | null) {
    emit('change', value?.value);
}
</script>
