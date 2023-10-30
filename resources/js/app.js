import './bootstrap';

import { createApp } from 'vue/dist/vue.esm-bundler';
import viewer from './components/viewer.vue';
import TwoWayStreamingApp from './components/TwoWayStreamingApp.vue';


const app = createApp({
    components: {
        'viewer': viewer,
        'twowaystreamingapp': TwoWayStreamingApp
    }
});

app.mount('#app');