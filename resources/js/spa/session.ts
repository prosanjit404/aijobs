import { reactive } from 'vue';
import { http, setCsrfToken } from './http';
import type { AuthResponse, SessionPayload, SessionUser } from './types';

interface SessionState {
    loaded: boolean;
    authenticated: boolean;
    user: SessionUser | null;
}

export const session = reactive<SessionState>({
    loaded: false,
    authenticated: false,
    user: null,
});

let pending: Promise<void> | null = null;

export async function loadSession(force = false): Promise<void> {
    if (session.loaded && !force) return;
    if (pending) return pending;

    pending = (async () => {
        const data = await http<SessionPayload>('/session');
        session.authenticated = data.authenticated;
        session.user = data.user;
        session.loaded = true;
        setCsrfToken(data.csrf_token);
    })();

    try {
        await pending;
    } finally {
        pending = null;
    }
}

export function setAuthenticated(data: AuthResponse): void {
    session.authenticated = Boolean(data.user);
    session.user = data.user ?? null;
    session.loaded = true;
    setCsrfToken(data.csrf_token);
}

export function clearSession(csrfToken?: string): void {
    session.authenticated = false;
    session.user = null;
    session.loaded = true;
    setCsrfToken(csrfToken);
}
