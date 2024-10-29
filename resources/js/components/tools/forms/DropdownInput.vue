<template>
    <div class="form-control text-base my-2">
        <label :for="id" v-if="label" class="label pt-0">
            <span class="label-text" :class="hasError ? 'text-error' : ''">{{ label }}<span v-if="required" class="text-error">*</span></span>
        </label>
        <div class="dropdown relative">
            <div
                class="input input-bordered flex items-center gap-4 w-full"
                :class="`input-${displayColor} ` + (hasError ? 'text-error ' : ' ') + (showInput ? 'rounded-b-none' : '')"
            >
                <slot name="left"></slot>
                <input
                    :style="{display: showInput ? 'inline-block': 'none'}"
                    ref="input"
                    v-model="searchText"
                    :id="id"
                    :name="name ?? id"
                    :required="required"
                    :disabled="disabled"
                    :readonly="readonly"
                    class="grow"
                    :placeholder="placeholder ?? 'Search...'"
                    @focus="onFocus"
                    @blur="onBlur"
                />
                <div v-if="!showInput" @click="onClick" class="w-full flex" :class="{disabled}">
                    <div v-if="selected?.key" class="grow"><slot name="item" v-bind="selected">{{ selected?.label }}</slot></div>
                    <div v-else class="placeholder-text">{{ placeholder ?? 'Search...' }}</div>
                </div>
                <div v-if="selected?.key && !noClear" @click="clearSelection" title="Clear selection" class="text-lg flex cursor-pointer"><i class="my-auto inline-block fas fa-times"/></div>
                <slot name="right"></slot>
            </div>
            <ul v-if="showList" class="w-full menu p-2 shadow bg-base-100 rounded-box dropdown-content z-[1] top-12 rounded-t-none max-h-80 flex-nowrap overflow-auto">
                <template v-if="options.length === 0">
                    <li>
                        <a><slot :search-text="searchText" name="empty-result">{{ searchText ? 'No results' : 'Please refine your search' }}</slot></a>
                    </li>
                </template>
                <template v-else>
                    <li class="dropdown-item" v-for="option in options" :key="option.key" @mousedown.prevent="select(option)">
                        <a><slot name="item" v-bind="option"><span v-html="withHighlight ? highlight(String(option.label)) : option.label"></span></slot></a>
                    </li>
                </template>
                <loading-mask  v-if="loading" />
            </ul>
        </div>
        <div v-if="helpText ?? (typeof displayError === 'string' && displayError.length)" class="label pb-0">
        <span class="label-text-alt" :class="hasError ? 'font-bold text-error' : 'italic'">{{
                (typeof displayError === 'string' && displayError.length) ? displayError : helpText
            }}</span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { nextTick, ref, watch, useTemplateRef, computed } from 'vue';
import { BaseFormInput, Option } from '@interfaces';
import { useDebouncedRef } from '@composables/useDebouncedRef';
import LoadingMask from '@tools/LoadingMask.vue';

interface Props extends BaseFormInput {
    options: Option[],
    loading?: boolean,
    withHighlight?: boolean,
    debounce?: number | boolean,
    noClear?: boolean
}

const props = defineProps<Props>();

const selected = defineModel<Option|null>();

const internalError = ref<string>();

const hasError = computed<boolean>(() => !!props.error || !!internalError.value);
const displayError = computed<undefined|string|boolean>(() => (typeof props.error === 'string' && props.error.length) ? props.error : internalError.value);
const displayColor = computed<undefined|string>(() => hasError.value ? 'error' : props.color);

const searchText = useDebouncedRef('', props.debounce);
const inputElement = useTemplateRef<HTMLInputElement>('input');

const showList = ref(false);
const showInput = ref(false);

const emit = defineEmits(['change', 'search']);

function onClick () {
    if (props.readonly || props.disabled) {
        return;
    }

    showInput.value = true;
    if (searchText.value.length > 0 || props.options.length === 0) {
        search();
    }

    nextTick(() => {
        inputElement.value?.focus();
    });
}

function onFocus () {
    if (props.readonly || props.disabled) {
        return;
    }
    showList.value = true;
}

function onBlur () {
    if (props.readonly || props.disabled) {
        return;
    }

    showList.value = false;
    showInput.value = false;

    if (selected.value === null || selected.value === undefined || selected.value?.key === undefined) {
        searchText.value = '';
        internalError.value = props.required ? 'This field is required' : '';
    }
}

function search () {
    if (props.readonly || props.disabled) {
        return;
    }

    emit('search', searchText.value);
}

function highlight (value: string) {
    if (searchText.value.length > 0) {
        const regex = new RegExp(`(${searchText.value.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&')})`, 'ig');
        return value.replace(regex, '<mark>$1</mark>');
    }
    return value;
}

function select (selectedOption: Option) {
    internalError.value = '';
    const changed = selected.value?.key !== selectedOption.key;
    selected.value = props.options.find(option => option.key === selectedOption.key) ?? null;
    nextTick(() => {
        onBlur();
    });

    if (changed) {
        emit('change', selected);
    }
}

function clearSelection (): void {
    if (props.readonly || props.disabled) {
        return;
    }

    internalError.value = '';
    selected.value = null;
    if (!props.required) {
        emit('change', selected);
    }
    nextTick(() => {
        onBlur();
    });
}

watch(searchText, search);

defineExpose({ select, highlight });
</script>

<style scoped>

</style>

<style>
.form-control mark {
    background: inherit;
    padding: inherit;
    color: inherit;
}

.dropdown-item mark {
    background-color: yellow;
    padding: 0;
}
</style>
