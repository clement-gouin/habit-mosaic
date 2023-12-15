import { defineStore } from 'pinia';
import { Notification } from '@interfaces';
import { ref } from 'vue';
import { AxiosError } from 'axios';

export const useNotificationsStore = defineStore('notifications', () => {
    const notifications = ref<Notification[]>([]);

    function showNotification (id: number): void {
        notifications.value.forEach(notification => {
            if (notification.id === id) {
                notification.show = true;
            }
        });
    }

    function hideNotification (id: number): void {
        notifications.value.forEach(notification => {
            if (notification.id === id) {
                notification.show = false;
            }
        });
    }

    function deleteNotification (id: number): void {
        const index = notifications.value.findIndex(notification => notification.id === id);
        if (index >= 0) {
            notifications.value.splice(index, 1);
        }
    }

    function notify (type: string, title: string, text: string): void {
        const id = Date.now();
        notifications.value.push({
            id,
            type,
            title,
            text,
            show: false
        });
        setTimeout(() => { showNotification(id); });
        setTimeout(() => { hideNotification(id); }, 8000);
        setTimeout(() => { deleteNotification(id); }, 10000);
    }

    function notifySuccess (text: string, title = 'Success'): void {
        notify('success', title, text);
    }

    function notifyError (text: string, title = 'Error'): void {
        notify('error', title, text);
    }

    function notifyAxiosError (error: AxiosError): never {
        if (error.message === 'canceled') {
            notifyError(
                error.response?.data?.message ?? error.response?.statusText ?? error.message,
                `Error ${error.response?.status ?? 0}`
            );
        }
        throw error;
    }

    return { notifications, notify, notifySuccess, notifyError, notifyAxiosError };
});
