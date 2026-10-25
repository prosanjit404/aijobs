export interface SessionUser {
    id: number;
    name: string;
    email: string;
}

export interface SessionPayload {
    authenticated: boolean;
    user: SessionUser | null;
    csrf_token: string;
}

export interface AuthResponse {
    success: boolean;
    message: string;
    csrf_token: string;
    user?: SessionUser;
}

export interface PageResponse {
    component: string;
    title: string;
    props: Record<string, unknown>;
}

export type ValidationErrors = Record<string, string[]>;
