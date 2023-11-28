<template>
  <div v-if="enabled" role="dialog">
    <div class="fade modal-backdrop" :class="visible ? 'show' : ''"></div>
    <div class="fade modal" role="dialog" tabindex="-1" :class="visible ? 'show' : ''" style="display:block">
      <div class="modal-dialog">
        <div class="modal-content" role="document">
          <div class="modal-header">
            <slot name="modal-header">
                <h4 class="modal-title">{{ title }}</h4>
              <button v-if="canClose" class="btn-close" type="button" @click.prevent="closeButton" />
            </slot>
          </div>
          <div class="modal-body">
            <slot></slot>
          </div>
          <div class="modal-footer">
            <slot name="modal-footer">
              <button v-if="canClose" type="button" class="btn btn-default" @click.prevent="closeButton"><span>{{ closeText ?? 'Close' }}</span></button>
              <button type="button" class="btn action-submit" :class="'btn-' + actionColor" @click.prevent="action"><span>{{ actionText ?? 'Confirm' }}</span></button>
            </slot>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface Props {
    title: string,
    canClose?: boolean,
    closeText?: string,
    actionText?: string,
    actionColor?: string,
    autoClose?: boolean,
}

interface Emits {
    (e: 'submit'): void
    (e: 'close'): void
}

const props = withDefaults(defineProps<Props>(), {
    canClose: true,
    actionColor: 'primary',
    autoClose: true
});

const visible = ref(false);
const enabled = ref(false);

const emit = defineEmits<Emits>();

function open (): void {
    enabled.value = true;
    setTimeout(() => {
        visible.value = true;
        document.body.classList.add('modal-open');
        document.addEventListener('keyup', keyUpEvent);
    });
}

function close (): void {
    visible.value = false;
    document.body.classList.remove('modal-open');
    setTimeout(() => {
        enabled.value = false;
        document.removeEventListener('keyup', keyUpEvent);
    });
}

function closeButton (): void {
    emit('close');
    close();
}

function action (): void {
    emit('submit');
    if (props.autoClose) {
        close();
    }
}

function keyUpEvent (event: KeyboardEvent) {
    if (event.key === 'Escape') {
        closeButton();
    }
}

defineExpose({ open, close });
</script>

<style scoped>
.modal, .modal-backdrop {
  z-index: -1050;
}

.modal.show, .modal-backdrop.show {
  z-index: 1050;
}
</style>
