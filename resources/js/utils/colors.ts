export function colorMix (ratio: number, from: string, to: string): string {
    return `color-mix(in srgb, ${to} ${100 * Math.min(ratio, 1)}%, ${from}) !important`;
}

export function darker (ratio: number, value: string): string {
    return colorMix(ratio, value.replace('!important', ''), '#212529');
}

export function lighter (ratio: number, value: string): string {
    return colorMix(ratio, value.replace('!important', ''), '#f8f9fa');
}

export function ratioColor (ratio: number, positive: boolean, root = '#f8f9fa'): string {
    return colorMix(ratio, root, positive ? 'oklch(var(--su))' : 'oklch(var(--er))');
}

export function referenceColor (value: number, reference: number, root = '#f8f9fa'): string {
    if (reference === 0) {
        reference = 1;
    }
    const ratio = value / reference;
    return ratioColor(Math.abs(ratio), value >= 0, root);
}

export function backgroundColor (value: string): string {
    return lighter(0.75, value);
}

export function borderColor (value: string): string {
    return lighter(0.6, darker(0.25, value));
}

export function textColor (value: string): string {
    return darker(0.75, value);
}
