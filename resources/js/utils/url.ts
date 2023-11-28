import qs from 'qs';
import { Base } from '@interfaces';

export function getURLParameters (): Base {
    return qs.parse(window.location.search.substring(1), {
        decoder: function (value: string) {
            if (/^-?\d+.\d+$/.test(value)) {
                return parseFloat(value);
            }
            if (/^-?\d+$/.test(value)) {
                return parseInt(value);
            }
            if (/^(true|false)$/.test(value)) {
                return value === 'true';
            }
            return value;
        }
    });
}

export function filterUrlParameters (parameters: Base, defaultParameters?: Base): Base {
    return Object.fromEntries(Object.entries(parameters)
        .filter(([key, value]: [string, unknown]) => value != null &&
        value !== '' &&
        (defaultParameters === undefined || value !== defaultParameters[key])));
}

export function toURLParameters (parameters: Base, defaultParameters?: Base): string {
    const outputParameters: Base = filterUrlParameters(parameters, defaultParameters);
    return qs.stringify(outputParameters, { encode: false, arrayFormat: 'brackets' });
}

export function saveURLParameters (parameters: Base, defaultParameters?: Base): void {
    window.history.pushState({}, '', `${window.location.pathname}?${toURLParameters(parameters, defaultParameters)}`);
}
