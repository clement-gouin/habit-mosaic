<template>
    <mosaic-background class="opacity-25 inset-0 z-0" />
    <div class="absolute bg-gradient-to-b from-primary-100 to-primary-50 opacity-25 inset-0 z-0"></div>
    <div class="hero bg-base-200 min-h-screen">
        <div class="hero-content flex-col lg:flex-row gap-20 lg:gap-30">
            <div class="text-center select-none">
                <h1 class="text-5xl lg:text-7xl font-bold text-primary-500">
                    <span>Habit</span><span class="text-primary-400">Mosaic</span>
                </h1>
                <h2 class="text-2xl italic text-gray-500">"Where habits take shape"</h2>
            </div>
            <div class="card bg-base-100 w-full max-w-sm shrink-0 shadow-2xl">
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
                    <div class="form-control">
                        <button class="btn btn-primary" @click="onSubmit" :disabled="!canSubmit">Receive my login link<span v-if="loading" class="loading loading-spinner loading-xs"></span></button>
                    </div>
                    <div v-if="outputMessage" class="text-success w-full text-center">
                        {{ outputMessage }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import axios from 'axios';
import TextInput from '@tools/forms/TextInput.vue';
import CheckboxInput from '@tools/forms/CheckboxInput.vue';
import MosaicBackground from '@components/login/MosaicBackground.vue';

const formData = ref({
    email: '',
    name: '',
    new: false
});

const errorMessage = ref('');
const outputMessage = ref('');
const loading = ref(false);

const canSubmit = computed(() => !loading.value && !outputMessage.value.length && formData.value.email && (!formData.value.new || formData.value.name));

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
