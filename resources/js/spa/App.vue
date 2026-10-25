<script setup lang="ts">
import { ref } from 'vue';
import {
    RouterLink,
    RouterView,
    useRoute,
    useRouter,
} from 'vue-router';

import { http } from './http';
import { page } from './page';
import {
    clearSession,
    loadSession,
    session,
} from './session';

import type { AuthResponse } from './types';

const router = useRouter();
const route = useRoute();

const busy = ref(false);
const actionError = ref('');

async function logout() {
    busy.value = true;
    actionError.value = '';

    try {
        const response = await http<AuthResponse>(
            '/logout',
            { method: 'POST' }
        );

        clearSession(response.csrf_token);

        await router.replace({
            name: 'login',
        });

    } catch (error) {
        actionError.value =
            error instanceof Error
                ? error.message
                : 'Logout failed.';
    } finally {
        busy.value = false;
    }
}

async function retry() {
    try {
        await loadSession(true);

        await router.replace({
            name: route.name ?? 'home',
            params: route.params,
            query: {
                ...route.query,
                retry: String(Date.now()),
            },
        });

    } catch (error) {
        page.error =
            error instanceof Error
                ? error.message
                : 'Unable to reconnect.';
    }
}
</script>

<template>
    <div class="spa-shell">

        <header class="spa-header">

            <RouterLink
                :to="{ name: 'home' }"
                class="spa-logo"
            >
                AI<span>Jobs</span>
            </RouterLink>

            <nav
                class="spa-nav"
                aria-label="Main navigation"
            >

                <RouterLink
                    :to="{ name: 'home' }"
                >
                    Home
                </RouterLink>

                <RouterLink
                    v-if="session.authenticated"
                    :to="{ name: 'dashboard' }"
                >
                    Dashboard
                </RouterLink>

                <template v-else>

                    <RouterLink
                        :to="{ name: 'login' }"
                    >
                        Login
                    </RouterLink>

                    <RouterLink
                        :to="{ name: 'register' }"
                        class="spa-nav-cta"
                    >
                        Get Started
                    </RouterLink>

                </template>

                <button
                    v-if="session.authenticated"
                    type="button"
                    :disabled="busy"
                    @click="logout"
                >
                    {{ busy ? 'Signing out...' : 'Logout' }}
                </button>

            </nav>
        </header>

        <div
            v-if="page.loading && page.loaded"
            class="spa-progress"
            aria-hidden="true"
        ></div>

        <p
            v-if="actionError"
            class="spa-alert"
            role="alert"
        >
            {{ actionError }}
        </p>

        <main
            v-if="!page.loaded"
            class="spa-state"
        >
            Loading...
        </main>

        <main
            v-else-if="page.error"
            class="spa-state"
        >
            <div class="spa-state-card">

                <h1>
                    {{ page.status ?? 'Error' }}
                </h1>

                <p>
                    {{ page.error }}
                </p>

                <button
                    type="button"
                    class="spa-button"
                    @click="retry"
                >
                    Try again
                </button>

            </div>
        </main>

        <RouterView
            v-else
            v-slot="{ Component, route }"
        >
            <component
                :is="Component"
                :key="route.path"
                v-bind="page.props"
            />
        </RouterView>

    </div>
</template>