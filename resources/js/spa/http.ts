import type { SessionPayload, ValidationErrors } from './types';

export class HttpError extends Error {
    constructor(
        message: string,
        public status: number,
        public errors: ValidationErrors = {},
    ) {
        super(message);
        this.name = 'HttpError';
    }
}

type Method = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';

interface HttpOptions {
    method?: Method;
    data?: unknown;
    retryCsrf?: boolean;
}

function csrfToken(): string {
    return document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
}

export function setCsrfToken(token?: string | null): void {
    if (!token) return;
    const meta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    if (meta) meta.content = token;
}

async function refreshCsrfToken(): Promise<boolean> {
    try {
        const response = await fetch('/session', {
            credentials: 'same-origin',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store',
        });
        if (!response.ok) return false;
        const payload = (await response.json()) as SessionPayload;
        setCsrfToken(payload.csrf_token);
        return Boolean(payload.csrf_token);
    } catch {
        return false;
    }
}

export async function http<T>(url: string, options: HttpOptions = {}): Promise<T> {
    const method = options.method ?? 'GET';
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    if (method !== 'GET') {
        headers['Content-Type'] = 'application/json';
        headers['X-CSRF-TOKEN'] = csrfToken();
    }

    let response: Response;
    try {
        response = await fetch(url, {
            method,
            credentials: 'same-origin',
            headers,
            body: method !== 'GET' && options.data !== undefined
                ? JSON.stringify(options.data)
                : undefined,
            cache: 'no-store',
        });
    } catch {
        throw new HttpError('Network connection failed. Check your internet and try again.', 0);
    }

    const payload: unknown = await response.json().catch(() => null);

    if (response.status === 419 && options.retryCsrf !== false) {
        if (await refreshCsrfToken()) {
            return http<T>(url, { ...options, retryCsrf: false });
        }
    }

    if (!response.ok) {
        const json = payload !== null && typeof payload === 'object'
            ? payload as { message?: string; errors?: ValidationErrors }
            : null;
        const defaultMessage = response.status === 401
            ? 'Please log in to continue.'
            : response.status === 419
                ? 'Session expired. Refresh the page and try again.'
                : `Request failed (${response.status}).`;
        throw new HttpError(json?.message ?? defaultMessage, response.status, json?.errors ?? {});
    }

    return payload as T;
}
