export function ratioColor (ratio: number, positive: boolean, variable: string, darker = '0%'): string {
    return `color-mix(in srgb, var(--bs-dark) ${darker}, color-mix(in srgb, var(--bs-${positive ? 'success' : 'danger'}-${variable}) ${100 * Math.min(ratio, 1)}%, var(--bs-light-${variable}))) !important`;
}

export function referenceColor (value: number, reference: number, variable: string, darker = '0%'): string {
    const ratio = value / reference;
    return ratioColor(Math.abs(ratio), ratio >= 0, variable, darker);
}
