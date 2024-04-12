import { onBeforeMount, onBeforeUnmount, Ref, ref } from 'vue';

interface BackgroundFetch {
    loading: Ref<boolean>
    forceFetch: () => Promise<void>
}

export function useBackgroundFetch<T> (promise: () => Promise<T>, callback: (data: T) => void, refreshRate = 10 * 1000, initialFetch = true): BackgroundFetch {
    const loading = ref<boolean>(false);
    const force = ref<number>(0);
    const timeoutId = ref<number>(0);

    async function getData (forceId = -1): Promise<void> {
        if (timeoutId.value > 0) {
            clearTimeout(timeoutId.value);
            timeoutId.value = 0;
        }
        await promise()
            .then(data => {
                if (force.value === forceId || !loading.value) {
                    loading.value = false;
                    callback(data);
                }
            })
            .finally(scheduleNextFetch);
    }

    function scheduleNextFetch (): void {
        if (timeoutId.value === 0) {
            timeoutId.value = setTimeout(() => { void getData(); }, refreshRate);
        }
    }

    async function forceFetch (): Promise<void> {
        loading.value = true;
        force.value = Date.now();
        await getData(force.value);
    }

    onBeforeMount(() => {
        if (initialFetch) {
            void forceFetch();
        } else {
            scheduleNextFetch();
        }
    });

    onBeforeUnmount(() => {
        clearTimeout(timeoutId.value);
        timeoutId.value = -1;
    });

    return { loading, forceFetch };
}
