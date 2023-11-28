import { InjectionKey, Ref } from 'vue';
import { Base } from '@interfaces';

export interface BsFormInjection extends Base {
    labelColSize?: Ref<number | undefined>
    inputWrapperColSize?: Ref<number | undefined>
    isHorizontal: Ref<boolean>
    isInline: Ref<boolean>
    isFloating: Ref<boolean>
}

export const BsFormKey: InjectionKey<BsFormInjection> = Symbol('bs-form');
