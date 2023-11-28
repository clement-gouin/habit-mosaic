<template>
    <div class="card">
        <div class="card-body">
                <bs-form horizontal floating>
                    <text-input
                        required
                        type="text"
                        id="email"
                        name="email"
                        label="Email"
                        v-model="formData.email"
                        :disabled="loading"
                        :placeholder="placeholder"
                        hide-help
                    />
                    <text-input
                        required
                        v-if="formData.new"
                        type="text"
                        id="name"
                        name="name"
                        label="Name"
                        v-model="formData.name"
                        :disabled="loading"
                        placeholder="Name"
                        hide-help
                    />
                </bs-form>
            <div v-if="errorMessage" class="invalid-feedback mb-1 d-block">
                {{ errorMessage }}
            </div>
            <div v-if="outputMessage" class="valid-feedback mb-1 d-block">
                {{ outputMessage }}
            </div>
            <div class="checkbox mb-3">
                <label>
                    <input
                        type="checkbox"
                        id="remember_me"
                        name="remember_me"
                        v-model="formData.new"
                        :disabled="loading"
                    >
                    I'm new
                </label>
            </div>
            <button
                type="submit"
                @click="onSubmit"
                class="w-100 btn btn-lg btn-primary"
                :class="{disabled: !canSubmit}">
                {{ formData.new ? 'Register' : 'Sign in' }}
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import axios from 'axios';
import TextInput from '@tools/forms/TextInput.vue';
import BsForm from '@tools/forms/BsForm.vue';

const formData = ref({
    email: '',
    name: '',
    new: false
});

const PLACEHOLDERS = ['to.stark', 'th.odinson', 'br.banner', 'st.rogers', 'cl.barton', 'na.romanoff'];
const placeholder = computed(() => PLACEHOLDERS[Math.floor(Math.random() * PLACEHOLDERS.length)] + '@avengers.com');
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
