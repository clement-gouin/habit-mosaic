import './bootstrap';

import { createApp, h } from 'vue';
import Components from './components/index.ts';
import VueCookies from 'vue-cookies';
import { createPinia } from 'pinia';
import AlertsContainer from './components/alerts/AlertsContainer.vue';
import axios from 'axios';
import { Service } from 'axios-middleware';

const element = document.getElementById('app');

if (element) {
    const componentName = element?.getAttribute('data-component') ?? '';
    const componentPropsRaw = element?.getAttribute('data-props') ?? '{}';

    const parsedData = JSON.parse(componentPropsRaw) ?? {};

    const app = createApp({ render: () => [h(Components[componentName], parsedData), h(AlertsContainer)] });

    element.removeAttribute('data-component');
    element.removeAttribute('data-props');

    const version = parsedData.version;

    const service = new Service(axios);

    service.register({
        onResponse (response) {
            const receivedVersion = response?.headers?.get('X-App-Version');
            if (receivedVersion && version && receivedVersion !== version) {
                window.location.reload();
            }
            return response;
        }
    });

    window.axios = axios;
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    app.use(createPinia());
    app.use(VueCookies, { expires: '365d' });

    app.mount('#app');
}
