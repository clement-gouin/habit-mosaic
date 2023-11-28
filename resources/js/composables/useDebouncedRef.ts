import { customRef, Ref } from 'vue';

export function useDebouncedRef<T> (value: T, delay: number | boolean | undefined = 200): Ref<T> {
    let timeout: number;

    return customRef<T>((track, trigger) => {
        return {
            get (): T {
                track();
                return value;
            },
            set (newValue: T) {
                if (delay === false || delay === undefined) {
                    value = newValue;
                    trigger();
                    return;
                }

                clearTimeout(timeout);
                delay = (delay === true) ? 200 : Number(delay);
                timeout = setTimeout(() => {
                    value = newValue;
                    trigger();
                }, delay);
            }
        };
    });
}
