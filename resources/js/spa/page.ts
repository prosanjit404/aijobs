import { reactive } from 'vue';

export interface PageState {
    loaded: boolean;
    loading: boolean;
    title: string;
    props: Record<string, unknown>;
    error: string | null;
    status: number | null;
}

export const page = reactive<PageState>({
    loaded: false,
    loading: false,
    title: '',
    props: {},
    error: null,
    status: null,
});
