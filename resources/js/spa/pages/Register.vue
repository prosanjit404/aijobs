<script setup lang="ts">
import { reactive, ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { http, HttpError } from '../http';
import { setAuthenticated } from '../session';
import type { AuthResponse, ValidationErrors } from '../types';

defineProps<{ site_name: string }>();
const router = useRouter();
const form = reactive({ name: '', email: '', password: '', password_confirmation: '' });
const busy = ref(false);
const message = ref('');
const errors = ref<ValidationErrors>({});

async function submit() {
    busy.value = true;
    message.value = '';
    errors.value = {};
    try {
        const response = await http<AuthResponse>('/register', { method: 'POST', data: form });
        setAuthenticated(response);
        await router.replace('/dashboard');
    } catch (error) {
        message.value = error instanceof Error ? error.message : 'Registration failed.';
        if (error instanceof HttpError) errors.value = error.errors;
    } finally {
        busy.value = false;
    }
}
</script>

<template>
    <main class="spa-auth-page">
        <section class="spa-auth-card">
            <span class="spa-eyebrow">GET STARTED</span>
            <h1>Create account</h1>
            <p>Join {{ site_name }} today.</p>
            <form @submit.prevent="submit">
                <p v-if="message" class="spa-form-error" role="alert">{{ message }}</p>
                <label for="name">Full name</label>
                <input id="name" v-model.trim="form.name" type="text" autocomplete="name" required>
                <small v-if="errors.name?.length" class="spa-field-error">{{ errors.name[0] }}</small>
                <label for="email">Email address</label>
                <input id="email" v-model.trim="form.email" type="email" autocomplete="email" required>
                <small v-if="errors.email?.length" class="spa-field-error">{{ errors.email[0] }}</small>
                <label for="password">Password</label>
                <input id="password" v-model="form.password" type="password" autocomplete="new-password" minlength="8" required>
                <small v-if="errors.password?.length" class="spa-field-error">{{ errors.password[0] }}</small>
                <label for="confirm">Confirm password</label>
                <input id="confirm" v-model="form.password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
                <button class="spa-button spa-button-full" type="submit" :disabled="busy">{{ busy ? 'Creating account...' : 'Create Account' }}</button>
            </form>
            <p class="spa-auth-foot">Already have an account? <RouterLink to="/login">Sign in</RouterLink></p>
        </section>
    </main>
</template>
