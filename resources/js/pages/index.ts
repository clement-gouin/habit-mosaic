import { defineAsyncComponent } from 'vue';

const Login = defineAsyncComponent(async () => await import('./Login.vue'));
const Dashboard = defineAsyncComponent(async () => await import('./Dashboard.vue'));
const DayView = defineAsyncComponent(async () => await import('./DayView.vue'));
const GraphView = defineAsyncComponent(async () => await import('./GraphView.vue'));
const TableView = defineAsyncComponent(async () => await import('./TableView.vue'));
const Configuration = defineAsyncComponent(async () => await import('./Configuration.vue'));

export default {
    Login,
    Dashboard,
    DayView,
    GraphView,
    TableView,
    Configuration
};
