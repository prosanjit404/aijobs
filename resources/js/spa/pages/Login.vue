<script setup lang="ts">
import { reactive, ref } from 'vue';
import { RouterLink, useRoute, useRouter } from 'vue-router';
import { http, HttpError } from '../http';
import { setAuthenticated } from '../session';
import type { AuthResponse, ValidationErrors } from '../types';

defineProps<{ site_name: string }>();
const router = useRouter();
const route = useRoute();
const form = reactive({ email: '', password: '' });
const busy = ref(false);
const message = ref('');
const errors = ref<ValidationErrors>({});

function safeRedirect(): string {
    const value = route.query.redirect;
    if (typeof value === 'string' && value.startsWith('/') && !value.startsWith('//') && !value.includes('\\')) return value;
    return '/dashboard';
}

async function submit() {
    busy.value = true;
    message.value = '';
    errors.value = {};
    try {
        const response = await http<AuthResponse>('/login', { method: 'POST', data: form });
        setAuthenticated(response);
        await router.replace(safeRedirect());
    } catch (error) {
        message.value = error instanceof Error ? error.message : 'Login failed.';
        if (error instanceof HttpError) errors.value = error.errors;
    } finally {
        busy.value = false;
    }
}
</script>

<template>
    <main class="spa-auth-page">
        <section class="spa-auth-card">
            <span class="spa-eyebrow">WELCOME BACK</span>
            <h1>Sign in</h1>
            <p>Access your {{ site_name }} account.</p>
            <form @submit.prevent="submit">
                <p v-if="message" class="spa-form-error" role="alert">{{ message }}</p>
                <label for="email">Email address</label>
                <input id="email" v-model.trim="form.email" type="email" autocomplete="email" required>
                <small v-if="errors.email?.length" class="spa-field-error">{{ errors.email[0] }}</small>
                <label for="password">Password</label>
                <input id="password" v-model="form.password" type="password" autocomplete="current-password" required>
                <small v-if="errors.password?.length" class="spa-field-error">{{ errors.password[0] }}</small>
                <button class="spa-button spa-button-full" type="submit" :disabled="busy">{{ busy ? 'Signing in...' : 'Sign In' }}</button>
            </form>
            <p class="spa-auth-foot">New to {{ site_name }}? <RouterLink to="/register">Create an account</RouterLink></p>
        </section>
    </main>
</template>
