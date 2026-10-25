<script setup lang="ts">
import { RouterLink } from 'vue-router';
import type { SessionUser } from '../types';

defineProps<{ user: SessionUser & { joined_at: string | null } }>();
</script>

<template>
    <main class="spa-main">
        <div class="spa-dashboard-head">
            <span class="spa-eyebrow">YOUR WORKSPACE</span>
            <h1>Dashboard</h1>
            <p>Welcome back, {{ user.name }}.</p>
        </div>
        <section class="spa-profile-grid">
            <article><span>Account ID</span><strong>#{{ user.id }}</strong></article>
            <article><span>Email address</span><strong>{{ user.email }}</strong></article>
            <article><span>Member since</span><strong>{{ user.joined_at || '—' }}</strong></article>
        </section>
        <RouterLink to="/home" class="spa-link">Back to home</RouterLink>
    </main>
</template>
