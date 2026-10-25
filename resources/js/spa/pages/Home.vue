<script setup lang="ts">
import { RouterLink } from 'vue-router';
import { session } from '../session';

defineProps<{ site_name: string; total_users: number; server_time: string }>();
</script>

<template>
    <main class="spa-main">
        <section class="spa-hero">
            <span class="spa-eyebrow">AI TASK & JOB PLATFORM</span>
            <h1>Discover work. <span>Build something great.</span></h1>
            <p>{{ site_name }} connects people with opportunities. This page is powered by Laravel server props and Vue hash navigation.</p>
            <div class="spa-actions">
                <RouterLink class="spa-button" :to="session.authenticated ? '/dashboard' : '/register'">{{ session.authenticated ? 'Open Dashboard' : 'Create Account' }}</RouterLink>
                <RouterLink v-if="!session.authenticated" class="spa-button spa-button-secondary" to="/login">Sign In</RouterLink>
            </div>
        </section>
        <section class="spa-stats" aria-label="Platform overview">
            <article><span>Registered users</span><strong>{{ total_users.toLocaleString('en-US') }}</strong></article>
            <article><span>Server time</span><strong class="spa-date">{{ server_time }}</strong></article>
            <article><span>Navigation mode</span><strong>Hash SPA</strong></article>
        </section>
    </main>
</template>
