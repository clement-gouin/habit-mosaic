import './bootstrap';

import { createApp, h } from 'vue';
import Components from './components/index.ts';
import { createPinia } from 'pinia';
import Notifications from './components/notifications/Notifications.vue';

const element = document.getElementById('app');

if (element) {
    const componentName = element?.getAttribute('data-component') ?? '';
    const componentPropsRaw = element?.getAttribute('data-props') ?? '{}';

    const parsedData = JSON.parse(componentPropsRaw) ?? {};

    const app = createApp({ render: () => [h(Components[componentName], parsedData), h(Notifications)] });

    element.removeAttribute('data-component');
    element.removeAttribute('data-props');

    app.use(createPinia());
    app.mount('#app');
}
