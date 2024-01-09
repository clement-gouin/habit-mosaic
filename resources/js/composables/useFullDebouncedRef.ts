import { customRef, ref, Ref } from 'vue';
import { Base } from '@interfaces';

export interface ClonedDebouncedRef<T> extends Base {
    value: Ref<T>
    rawValue: Ref<T>
    isLoading: Ref<boolean>
}

export function useFullDebouncedRef<T> (value: T, delay: number | boolean | undefined = 200): ClonedDebouncedRef<T> {
    let timeout: number;

    const rawValue = ref<T>(value);
    const isLoading = ref<boolean>(false);

    const debouncedValue = customRef<T>((track, trigger) => {
        return {
            get (): T {
                track();
                return value;
            },
            set (newValue: T) {
                rawValue.value = newValue;
                if (delay === false || delay === undefined) {
                    value = newValue;
                    trigger();
                    return;
                }

                isLoading.value = true;
                clearTimeout(timeout);
                delay = (delay === true) ? 200 : Number(delay);
                timeout = setTimeout(() => {
                    value = newValue;
                    trigger();
                    isLoading.value = false;
                }, delay);
            }
        };
    });

    return { value: debouncedValue, rawValue, isLoading };
}
