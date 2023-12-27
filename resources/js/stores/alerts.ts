import { defineStore } from 'pinia';
import { Alert } from '@interfaces';
import { ref } from 'vue';
import { AxiosError } from 'axios';

export const useAlertsStore = defineStore('alerts', () => {
    const alerts = ref<Alert[]>([]);

    function showAlert (id: number): void {
        alerts.value.forEach(notification => {
            if (notification.id === id) {
                notification.show = true;
            }
        });
    }

    function hideAlert (id: number): void {
        alerts.value.forEach(notification => {
            if (notification.id === id) {
                notification.show = false;
            }
        });
    }

    function deleteAlert (id: number): void {
        const index = alerts.value.findIndex(notification => notification.id === id);
        if (index >= 0) {
            alerts.value.splice(index, 1);
        }
    }

    function alert (type: string, text: string, title?: string): void {
        const id = Date.now();
        alerts.value.push({
            id,
            type,
            title,
            text,
            show: false
        });
        setTimeout(() => { showAlert(id); });
        setTimeout(() => { hideAlert(id); }, 5000);
        setTimeout(() => { deleteAlert(id); }, 6000);
    }

    function alertSuccess (text: string, title?: string): void {
        alert('success', text, title);
    }

    function alertError (text: string, title?: string): void {
        alert('error', text, title);
    }

    function alertAxiosError (error: AxiosError): never {
        if (error.message !== 'canceled' && error.message !== 'Request aborted') {
            alertError(
                error.response?.data?.message ?? error.response?.statusText ?? error.message,
                `Error ${error.response?.status ?? 0}`
            );
        }
        throw error;
    }

    return { alerts, alert, alertSuccess, alertError, alertAxiosError };
});
