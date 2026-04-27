import '../css/app.css';
import './bootstrap';

import { createApp } from 'vue';
import router from './router';
import App from './App.vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

createApp(App)
    .use(router)
    .use(ZiggyVue)
    .mount('#app');
