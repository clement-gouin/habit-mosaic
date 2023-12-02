import './bootstrap';

import { createApp, h } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import Components from './components/index.ts';

const element = document.getElementById('app');

if (element) {
    const componentName = element?.getAttribute('data-component') ?? '';
    const componentPropsRaw = element?.getAttribute('data-props') ?? '{}';

    const parsedData = JSON.parse(componentPropsRaw) ?? {};

    const app = createApp({ render: () => h(Components[componentName], parsedData) });

    app.component('font-awesome-icon', FontAwesomeIcon);

    element.removeAttribute('data-component');
    element.removeAttribute('data-props');

    app.mount('#app');
}
