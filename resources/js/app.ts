import { createApp, type DirectiveBinding } from 'vue';
import App from './App.vue';
import router from './router';

const app = createApp(App);

app.directive('focus', {
    mounted(
        el: HTMLElement,
        binding: DirectiveBinding<boolean | undefined>
    ) {
        if (binding.value !== false) {
            el.focus();
        }
    },
});

app.use(router);

app.mount('#app');