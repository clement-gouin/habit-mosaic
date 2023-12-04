import { computed, ComputedRef, inject, Ref, ref } from 'vue';
import { BsFormKey } from '@symbols';

interface BsForm {
    labelClass: ComputedRef<Record<string, boolean>>
    inputWrapperClass: ComputedRef<Record<string, boolean>>
    isHorizontal: Ref<boolean>
    isInline: Ref<boolean>
    isFloating: Ref<boolean>
}

export function useBsForm (props?: Record<string, unknown>): BsForm {
    const { labelColSize, inputWrapperColSize, isHorizontal, isInline, isFloating } = inject(BsFormKey, {
        labelColSize: ref(4),
        isHorizontal: ref(true),
        isInline: ref(false),
        isFloating: ref(false)
    });

    const labelClass = computed<Record<string, boolean>>(() => {
        const col = (props?.labelColSize as number) ??
      labelColSize?.value ??
      12 - ((props?.inputWrapperColSize as number) ?? inputWrapperColSize?.value ?? 8);

        return {
            'col-form-label': !isFloating.value && isHorizontal.value,
            'form-label': isFloating.value || !isHorizontal.value,
            [`col-sm-${col}`]: isHorizontal.value && !isFloating.value
        };
    });

    const inputWrapperClass = computed<Record<string, boolean>>(() => {
        const col = (props?.inputWrapperColSize as number) ??
      inputWrapperColSize?.value ??
      12 - ((props?.labelColSize as number) ?? labelColSize?.value ?? 4);

        return {
            'input-column': !isFloating.value,
            [`col-sm-${col}`]: isHorizontal.value && !isFloating.value,
            'form-floating': isFloating.value
        };
    });

    return { labelClass, inputWrapperClass, isHorizontal, isInline, isFloating };
}
