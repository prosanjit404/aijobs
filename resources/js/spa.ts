import { createApp } from 'vue';
import App from './spa/App.vue';
import router from './spa/router';
import '../css/app.css';
import '../css/spa.css';

createApp(App).use(router).mount('#app');
