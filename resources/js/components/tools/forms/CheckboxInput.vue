<template>
    <div class="form-group" :class="{'row': isHorizontal && !isFloating}">
        <label v-if="!isFloating" :class="labelClass" :for="name">
            <template v-if="helpText">
                <Tooltip :text="helpText">{{ label }}<span v-if="required" class="text-danger">*</span>&nbsp;<span
                    class="badge">?</span></Tooltip>
            </template>
            <template v-else>
                {{ label }}<span v-if="required" class="text-danger">*</span>
            </template>
        </label>
        <div class="form-check form-switch" :class="inputWrapperClass">
            <input
                ref="input"
                :name="name"
                type="checkbox"
                id="checkbox"
                class="form-check-input align-text-bottom fs-4"
                style="margin-left: -1.5rem"
                v-model="checked"
                :disabled="disabled"
                :required="required"
                @change="onChange"
                @click.stop
            >
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import Tooltip from '@tools/Tooltip.vue';
import { useBsForm } from '@composables/useBsForm';

interface Props {
    name: string,
    checked?: boolean,
    placeholder?: string,
    label?: string,
    disabled?: boolean,
    required?: boolean,
    helpText?: string,

    labelColSize?: number,
    inputWrapperColSize?: number,
}

const props = defineProps<Props>();
const checked = ref<boolean>(props.checked);

const emit = defineEmits(['change', 'update:checked']);

const { labelClass, inputWrapperClass, isHorizontal, isFloating } = useBsForm(props);

const input = ref<HTMLInputElement|null>(null);

function onChange (event: InputEvent) {
    const inputField = event.currentTarget as HTMLInputElement;
    emit('change', inputField.checked);
}

watch(() => props.checked, () => {
    checked.value = props.checked;
});
</script>
