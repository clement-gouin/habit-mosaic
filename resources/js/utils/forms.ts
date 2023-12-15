import { AxiosError } from 'axios';
import { ErrorResponse } from '@interfaces';

export function handleFormErrors (error: AxiosError): Record<string, string> {
    if (error.response?.status === 400 || error.response?.status === 422) {
        const inErrors = (error.response?.data as ErrorResponse)?.errors;
        const outErrors: Record<string, string> = {};
        Object.keys(inErrors).forEach((key: string) => {
            outErrors[key] = inErrors[key].join('\n');

            if (key.includes('.')) {
                const [parentKey] = key.split('.');
                outErrors[parentKey] = outErrors[key];
            }
        });
        return outErrors;
    }
    return {};
}

export function validateEmail (value: string): string | null {
    if (/^\w+([.-]\w+)*@\w+([.-]\w+)*\.\w+$/.test(value)) {
        return value;
    }
    return null;
}
