<template>
    <dialog ref="dialog" class="modal" @close="onClose">
        <div class="modal-box">
            <slot name="modal-header">
                <h3 v-if="title" class="text-lg font-bold pb-3">{{ title }}</h3>
            </slot>
            <slot></slot>
            <div class="modal-action">
                <slot name="modal-footer">
                    <button v-if="canClose"  @click="onClose" class="btn ml-4">{{closeText}}</button>
                    <button v-if="canSubmit"  @click="onSubmit" class="btn ml-4" :class="`btn-${actionColor}`">{{actionText}}</button>
                </slot>
            </div>
        </div>
        <form v-if="autoClose && canClose" method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface Props {
    title?: string
    canClose?: boolean
    closeText?: string
    actionText?: string
    actionColor?: string
    autoClose?: boolean
    canSubmit?: boolean
    noBody?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    closeText: 'Cancel',
    actionText: 'OK',
    canClose: true,
    canSubmit: true,
    actionColor: 'primary',
    autoClose: true
});

interface Emits {
    (e: 'submit'): void
    (e: 'close'): void
}

const emit = defineEmits<Emits>();

const dialog = ref<HTMLDialogElement | null>(null);
const displayed = ref<boolean>(false);

function onClose (): void {
    if (displayed.value) {
        if (!props.canClose) {
            open();
        } else {
            displayed.value = false;
            close();
            emit('close');
        }
    }
}

function onSubmit (): void {
    if (displayed.value) {
        if (props.autoClose) {
            displayed.value = false;
            close();
        }
        emit('submit');
    }
}

function open (): void {
    displayed.value = true;
    dialog.value?.showModal();
}

function close (): void {
    displayed.value = false;
    dialog.value?.close();
}

defineExpose({ open, close, submit: onSubmit });
</script>
