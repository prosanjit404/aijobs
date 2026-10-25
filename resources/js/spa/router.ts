import {
    createRouter,
    createWebHashHistory,
    type RouteRecordRaw,
} from 'vue-router';

import { http, HttpError } from './http';
import { page } from './page';
import {
    clearSession,
    loadSession,
    session,
} from './session';

import type { PageResponse } from './types';

type PageRouteName =
    | 'spa.pages.home'
    | 'spa.pages.login'
    | 'spa.pages.register'
    | 'spa.pages.dashboard';

declare global {
    interface Window {
        __SPA_ROUTES__?: Partial<Record<PageRouteName, string>>;
    }
}

declare module 'vue-router' {
    interface RouteMeta {
        title?: string;
        dataRoute?: PageRouteName;
        requiresAuth?: boolean;
        guestOnly?: boolean;
    }
}

const routes: RouteRecordRaw[] = [
    {
        path: '/',
        redirect: { name: 'home' },
    },
    {
        path: '/home',
        name: 'home',
        component: () => import('./pages/Home.vue'),
        meta: {
            title: 'Home',
            dataRoute: 'spa.pages.home',
        },
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('./pages/Login.vue'),
        meta: {
            title: 'Login',
            dataRoute: 'spa.pages.login',
            guestOnly: true,
        },
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('./pages/Register.vue'),
        meta: {
            title: 'Register',
            dataRoute: 'spa.pages.register',
            guestOnly: true,
        },
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('./pages/Dashboard.vue'),
        meta: {
            title: 'Dashboard',
            dataRoute: 'spa.pages.dashboard',
            requiresAuth: true,
        },
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('./pages/NotFound.vue'),
        meta: {
            title: 'Page Not Found',
        },
    },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
    scrollBehavior: () => ({ top: 0 }),
});

let navigationId = 0;

function getPageUrl(name: PageRouteName): string {
    const url = window.__SPA_ROUTES__?.[name];

    if (!url) {
        throw new Error(`Laravel route not found: ${name}`);
    }

    return url;
}

router.beforeEach(async (to) => {
    const id = ++navigationId;

    page.loading = true;

    try {
        await loadSession();

        if (id !== navigationId) {
            return false;
        }

        if (to.meta.requiresAuth && !session.authenticated) {
            return {
                name: 'login',
                query: {
                    redirect: to.fullPath,
                },
            };
        }

        if (to.meta.guestOnly && session.authenticated) {
            return {
                name: 'dashboard',
            };
        }

        if (to.meta.dataRoute) {
            const url = getPageUrl(to.meta.dataRoute);

            const response = await http<PageResponse>(url);

            if (id !== navigationId) {
                return false;
            }

            page.props = response.props;
            page.title = response.title;
        } else {
            page.props = {};
            page.title = to.meta.title ?? 'Page Not Found';
        }

        page.error = null;
        page.status = null;
        page.loaded = true;

        document.title = `${page.title} - ${
            import.meta.env.VITE_APP_NAME || 'AI Jobs'
        }`;

        return true;
    } catch (error) {
        if (id !== navigationId) {
            return false;
        }

        if (
            error instanceof HttpError &&
            error.status === 401 &&
            to.meta.requiresAuth
        ) {
            clearSession();

            return {
                name: 'login',
                query: {
                    redirect: to.fullPath,
                },
            };
        }

        page.error =
            error instanceof Error
                ? error.message
                : 'Unable to load this page.';

        page.status =
            error instanceof HttpError
                ? error.status
                : null;

        page.loaded = true;

        return true;
    } finally {
        if (id === navigationId) {
            page.loading = false;
        }
    }
});

export default router;