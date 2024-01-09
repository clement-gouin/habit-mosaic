import { Base } from '@interfaces';
import { Ref, ref } from 'vue';

export interface AsyncState<T> extends Base {
    state: Ref<T>
    isReady: Ref<boolean>
    isLoading: Ref<boolean>
    error: Ref<unknown>
    updateState: () => Promise<void>
}

export function useAsyncState<T> (promise: () => Promise<T>, initialState: T | undefined = undefined, immediate = true): AsyncState<T> {
    const state = ref<T | undefined>(initialState) as Ref<T>;
    const isReady = ref<boolean>(!immediate);
    const isLoading = ref<boolean>(false);
    const error = ref<unknown>(undefined);

    async function updateState (initial = false): Promise<void> {
        error.value = undefined;
        if (initial) {
            isReady.value = false;
        }
        isLoading.value = true;

        try {
            state.value = await promise();
            isReady.value = true;
        } catch (e) {
            error.value = e;
        }

        isLoading.value = false;
    }

    if (immediate) {
        void updateState(true);
    }

    return {
        state,
        isReady,
        isLoading,
        error,
        updateState
    };
}
