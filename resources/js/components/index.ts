import { defineAsyncComponent } from 'vue';

const Login = defineAsyncComponent(async () => await import('./auth/Login.vue'));
const Dashboard = defineAsyncComponent(async () => await import('./home/Dashboard.vue'));
const DayView = defineAsyncComponent(async () => await import('./day/DayView.vue'));
const TableView = defineAsyncComponent(async () => await import('./table/TableView.vue'));
const Configuration = defineAsyncComponent(async () => await import('./config/Configuration.vue'));

export default {
    Login,
    Dashboard,
    DayView,
    TableView,
    Configuration
};
