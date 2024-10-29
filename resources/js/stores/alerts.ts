import { defineStore } from 'pinia';
import { Alert, AlertType } from '@interfaces';
import { ref } from 'vue';
import axios, { AxiosError } from 'axios';

export const useAlertsStore = defineStore('alerts', () => {
    const alerts = ref<Alert[]>([]);

    function fadeIn (id: number): void {
        alerts.value.forEach(notification => {
            if (notification.id === id) {
                notification.fade = false;
            }
        });
    }

    function fadeOut (id: number): void {
        alerts.value.forEach(notification => {
            if (notification.id === id) {
                notification.fade = true;
            }
        });
    }

    function deleteNotification (id: number): void {
        const index = alerts.value.findIndex(notification => notification.id === id);
        if (index >= 0) {
            alerts.value.splice(index, 1);
        }
    }

    function alert (type: AlertType, text: string, title?: string): void {
        const id = Math.random();
        alerts.value.push({
            id,
            type,
            title,
            text,
            fade: true
        });
        // https://ux.stackexchange.com/questions/11203/how-long-should-a-temporary-notification-toast-appear
        const duration = Math.max(Math.min(((title ?? '') + text).length * 50, 2000), 7000);
        setTimeout(() => { fadeIn(id); });
        setTimeout(() => { fadeOut(id); }, 200 + duration);
        setTimeout(() => { deleteNotification(id); }, 200 + duration + 700);
    }

    function alertInfo (text: string, title?: string): void {
        alert(AlertType.Info, text, title);
    }

    function alertSuccess (text: string, title?: string): void {
        alert(AlertType.Success, text, title);
    }

    function alertWarning (text: string, title?: string): void {
        alert(AlertType.Warning, text, title);
    }

    function alertError (text: string, title?: string): void {
        alert(AlertType.Error, text, title);
    }

    axios.interceptors.response.use(
        response => {
            return response;
        },
        async (error: AxiosError) => {
            if (error.message !== 'canceled' && error.message !== 'Request aborted') {
                alertError(
                    (error.response?.data as { message: string })?.message ?? error.response?.statusText ?? error.message,
                    `Error ${error.response?.status ?? 0}`
                );
            }
            throw error;
        }
    );

    return { alerts, alert, alertInfo, alertSuccess, alertWarning, alertError };
});
