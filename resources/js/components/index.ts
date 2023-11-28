import { defineAsyncComponent } from 'vue';

const Login = defineAsyncComponent(async () => await import('./auth/Login.vue'));
const Dashboard = defineAsyncComponent(async () => await import('./home/Dashboard.vue'));

export default {
    Login,
    Dashboard
};
