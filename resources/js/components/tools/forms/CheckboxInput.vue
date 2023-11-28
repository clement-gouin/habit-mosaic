<template>
  <div class="form-group">
    <div class="input-column col-sm-9 col-sm-offset-3 checkbox">
      <input
          ref="input"
          :name="name"
          type="checkbox"
          id="checkbox"
          v-model="checked"
          :disabled="disabled"
          :required="required"
          @change="onChange"
          @click.stop
      >
      <label for="checkbox">
        <template v-if="helpText">
          <Tooltip :text="helpText">{{ label }}<span v-if="required" class="text-danger">*</span>&nbsp;<span class="badge">?</span></Tooltip>
        </template>
        <template v-else>
          {{ label }}<span v-if="required" class="text-danger">*</span>
        </template>
      </label>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import Tooltip from '@tools/Tooltip.vue';

interface Props {
    name: string,
    checked?: boolean,
    placeholder?: string,
    label?: string,
    disabled?: boolean,
    required?: boolean,
    helpText?: string,
}

const props = defineProps<Props>();
const checked = ref<boolean>(props.checked);

const emit = defineEmits(['change', 'update:checked']);

const input = ref<HTMLInputElement|null>(null);

function onChange (event: InputEvent) {
    const inputField = event.currentTarget as HTMLInputElement;
    emit('change', inputField.checked);
}

watch(() => props.checked, () => {
    checked.value = props.checked;
});
</script>
