<template>
    <dropdown-input
        v-model="icon"
        :id="id"
        :name="name"
        :label="label"
        :placeholder="placeholder ?? 'Search icon...'"
        :disabled="disabled"
        :required="required"
        :readonly="readonly"
        :help-text="helpText"
        :color="color"
        :error="error"
        :options="options"
        @search="onSearch"
        :debounce="500"
        with-highlight
    >
        <template #left>
            <slot name="left"></slot>
        </template>
        <template #item="option">
            <i :class="mapToClassName(option.value)"></i> {{ option.value }}
        </template>
        <template #right>
            <slot name="right"></slot>
        </template>
    </dropdown-input>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { BaseFormInput, Option } from '@interfaces';
import { searchIcon, mapToClassName } from '@utils/icons';
import DropdownInput from '@tools/forms/DropdownInput.vue';

type Props = BaseFormInput;

function iconNameToOption (name: string): Option<string> {
    return { key: name, label: name, value: name };
}

const iconModel = defineModel<string|null>();

const icon = computed<Option<string>|null>({
    get (): Option<string> | null {
        return iconModel.value == null ? null : iconNameToOption(iconModel.value);
    },
    set (selected: Option<string> | null) {
        iconModel.value = (selected?.value as string|undefined) ?? null;
        emit('change', iconModel.value);
    }
});

defineProps<Props>();

const emit = defineEmits(['change']);

const options = ref<Option<string>[]>([]);
const loading = ref(false);

async function onSearch (value = '') {
    if (value && value.length > 2) {
        loading.value = true;
        options.value = searchIcon(value).map(iconNameToOption);
    } else {
        options.value = [];
    }
}
</script>
