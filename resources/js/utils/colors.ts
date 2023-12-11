export function darker (ratio: number, value: string): string {
    return `color-mix(in srgb, ${value.replace('!important', '')}, var(--bs-dark) ${100 * Math.min(ratio, 1)}%) !important`;
}

export function lighter (ratio: number, value: string): string {
    return `color-mix(in srgb, ${value.replace('!important', '')} var(--bs-light) ${100 * Math.min(ratio, 1)}%) !important`;
}

export function colorMix (ratio: number, from: string, to: string, variable: string): string {
    return `color-mix(in srgb, var(--bs-${to}${variable}) ${100 * Math.min(ratio, 1)}%, var(--bs-${from}${variable})) !important`;
}

export function ratioColor (ratio: number, positive: boolean, variable = '', root = 'light'): string {
    return colorMix(ratio, root, positive ? 'success' : 'danger', variable.length > 0 ? '-' + variable : '');
}

export function referenceColor (value: number, reference: number, variable = '', root = 'light'): string {
    const ratio = value / reference;
    return ratioColor(Math.abs(ratio), ratio >= 0, variable, root);
}
