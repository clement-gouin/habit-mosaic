<template>
    <div class="card-body">
        <text-input
            required
            icon="envelope"
            placeholder="Email"
            :disabled="loading"
            v-model="formData.email"
            :error="errorMessage"
            type="email"
        />
        <text-input
            v-if="formData.new"
            type="name"
            required
            icon="user"
            placeholder="Name"
            :disabled="loading"
            v-model="formData.name"
            :error="!!errorMessage"
        />
        <checkbox-input
            label="I'm new"
            :error="!!errorMessage"
            :disabled="loading"
            v-model="formData.new"
        />
        <div class="form-control mt-6">
            <button class="btn btn-primary" @click="onSubmit" :class="{disabled: !canSubmit}">Entrer <span v-if="loading" class="loading loading-spinner loading-xs"></span></button>
        </div>
        <div v-if="outputMessage" class="text-success w-full text-center">
            {{ outputMessage }}
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import axios from 'axios';
import TextInput from '@tools/forms/TextInput.vue';
import CheckboxInput from '@tools/forms/CheckboxInput.vue';

const formData = ref({
    email: '',
    name: '',
    new: false
});

const errorMessage = ref('');
const outputMessage = ref('');
const loading = ref(false);

const canSubmit = computed(() => !loading.value && formData.value.email && (!formData.value.new || formData.value.name));

function onSubmit () {
    loading.value = true;
    errorMessage.value = '';
    outputMessage.value = '';
    axios.post('/', formData.value)
        .then(response => {
            outputMessage.value = response.data.message;
        })
        .catch(e => {
            errorMessage.value = e.response.data.message ?? 'An error has occurred';
        })
        .finally(() => {
            loading.value = false;
        });
}
</script>

<style scoped>
  .card {
    background-color: #f5f5f5 !important;
  }
</style>
