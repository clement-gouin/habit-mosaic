<template>
    <div :id="`dropdown-input-${name}`" class="form-group" :class="{ 'has-error': error || internalError, 'has-feedback': true }">
        <slot name="label">
            <label :class="labelClass" :for="name">
                <template v-if="helpText">
                    <Tooltip :text="helpText">{{ label }}<span v-if="required" class="text-danger">*</span>&nbsp;<span class="badge">?</span></Tooltip>
                </template>
                <template v-else>
                    {{ label }}<span v-if="required" class="text-danger">*</span>
                </template>
            </label>
        </slot>
        <component :is="isHorizontal ? 'div' : 'template'" :class="inputWrapperClass">
            <div :class="$slots.addon ? 'input-group' : ''">
                <div v-if="!showInput" class="form-control" @click="onClick" :class="{disabled}">
                    <div v-if="selected?.key"><slot name="item" v-bind="selected">{{ selected?.label }}</slot></div>
                    <div v-else class="placeholder-text">{{ placeholder ?? 'Search...' }}</div>
                </div>
                <input
                    v-else
                    ref="input"
                    :name="name + '_label'"
                    type="text"
                    class="form-control"
                    v-model="searchText"
                    :disabled="disabled"
                    :aria-describedby="'help-' + name"
                    :required="required"
                    :readonly="readonly"
                    :placeholder="placeholder ?? 'Search...'"
                    @focus="onFocus"
                    @blur="onBlur"
                />
                <slot name="addon"></slot>
                <span
                    :style="{right: cursorOffset}"
                    v-if="selected?.key && !readonly && !disabled"
                    @click="clearSelection"
                    class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true">
                </span>
                <span v-else-if="!readonly && !disabled" :style="{right: cursorOffset}" class="glyphicon glyphicon-chevron-down form-control-feedback" aria-hidden="true" @click="onFocus"></span>
            </div>

            <ul ref="menu" class="dropdown-menu" :style="{ display: showList ? 'block' : 'none', ...floatingStyles}">
                <template v-if="options.length === 0">
                    <li class="dropdown-item disabled">
                        <a><slot :search-text="searchText" name="empty-result">{{ searchText ? 'No results' : 'Please refine your search' }}</slot></a>
                    </li>
                </template>
                <template v-else>
                    <li class="dropdown-item" v-for="option in options" :key="option.key" @mousedown.prevent="select(option)">
                        <a><slot name="item" v-bind="option"><span v-html="withHighlight ? highlight(String(option.label)) : option.label"></span></slot></a>
                    </li>
                </template>
                <div class="loading" v-if="loading" >
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </ul>
            <span v-if="error" :id="`help-${name}`" class="help-block">{{ error }}</span>
            <span v-else-if="internalError" :id="`help-${name}`" class="help-block">{{ internalError }}</span>
            <span v-else-if="notice || $slots.notice" :id="`help-${name}`" class="help-block"><slot name="notice">{{ notice }}</slot></span>
        </component>
    </div>
</template>

<script setup lang="ts">
import { computed, nextTick, ref, watch } from 'vue';
import Tooltip from '@tools/Tooltip.vue';
import { Option } from '@interfaces';
import { useBsForm } from '@composables/useBsForm';
import { useDebouncedRef } from '@composables/useDebouncedRef';
import { autoUpdate, flip, shift, useFloating } from '@floating-ui/vue';

interface Props {
    name: string,
    modelValue: Option|null
    placeholder?: string,
    label?: string,
    helpText?: string,
    error?: string,
    disabled?: boolean,
    required?: boolean,
    readonly?: boolean,
    notice?: string,
    cursorOffset?: string,

    options: Option[],
    loading?: boolean,
    withHighlight?: boolean,
    debounce?: number | boolean,
    labelColSize?: number,
    inputWrapperColSize?: number,

}

const props = withDefaults(defineProps<Props>(), { cursorOffset: '1em' });

const searchText = useDebouncedRef('', props.debounce);
const input = ref<HTMLInputElement|null>(null);
const internalError = ref<string|null>(null);

const menu = ref(null);
const { floatingStyles } = useFloating(input, menu, {
    placement: 'bottom-start',
    middleware: [flip(), shift()],
    whileElementsMounted: autoUpdate
});

const showList = ref(false);
const showInput = ref(false);

const emit = defineEmits(['update:modelValue', 'change', 'search']);

const { labelClass, inputWrapperClass, isHorizontal } = useBsForm(props);

const selected = computed<Option|null>({
    get (): Option|null { return props.modelValue; },
    set (value: Option|null) { emit('update:modelValue', value); }
});

function onClick () {
    if (props.readonly || props.disabled) {
        return;
    }

    showInput.value = true;
    if (searchText.value.length > 0 || props.options.length === 0) {
        search();
    }

    nextTick(() => {
        input.value?.focus();
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

    if (selected.value === null || selected.value?.key === undefined) {
        searchText.value = '';
        internalError.value = props.required ? 'This field is required' : null;
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
    cleanErrors();
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

    cleanErrors();
    selected.value = null;
    if (!props.required) {
        emit('change', selected);
    }
    nextTick(() => {
        onBlur();
    });
}

function cleanErrors () {
    internalError.value = null;
}

watch(searchText, search);

defineExpose({ select, highlight });
</script>

<style scoped lang="scss">
ul.dropdown-menu {
    max-height: 20em;
    overflow: scroll;
    width: 100%;
}

.dropdown-item {
    cursor: pointer;
    &.disabled {
        cursor: none;
        font-style: italic;

    }
}

.has-feedback .form-control-feedback {
    user-select: none;
    pointer-events: all;
    cursor: pointer;
    right: 0;
    z-index: 1000;
    &.glyphicon-remove {
        color: initial;
    }
}

.form-control .placeholder-text {
    color: #999;
}

.form-control.disabled {
    opacity: 0.3;
}

.loading {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background: white;
    opacity: 0.8;

    .spinner-border {
        position: absolute;
        top: 50%;
        left: 50%;
        margin: -1rem 0 0 -1rem;
    }
}

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
