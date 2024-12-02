import { Color } from '@interfaces';

function componentToHex (c: number): string {
    const hex = c.toString(16);
    return hex.length === 1 ? '0' + hex : hex;
}

function rgbToHex (color: Color): string {
    return '#' + componentToHex(color.r) + componentToHex(color.g) + componentToHex(color.b);
}

function hexToRgb (hex: string): Color {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return (result != null)
        ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        }
        : { r: 0, g: 0, b: 0 };
}

export function colorMix (ratio: number, from: string, to: string): string {
    const fromColor = hexToRgb(from);
    const toColor = hexToRgb(to);
    ratio = Math.min(1, ratio);
    return rgbToHex({
        r: Math.round(toColor.r * ratio + fromColor.r * (1 - ratio)),
        g: Math.round(toColor.g * ratio + fromColor.g * (1 - ratio)),
        b: Math.round(toColor.b * ratio + fromColor.b * (1 - ratio))
    });
}

export function darker (ratio: number, value: string): string {
    return colorMix(ratio, value, '#212529');
}

export function lighter (ratio: number, value: string): string {
    return colorMix(ratio, value, '#ffffff');
}

export function ratioColor (ratio: number, positive: boolean, root = '#ffffff'): string {
    return colorMix(ratio, root, positive ? '#00a96e' : '#ff6368');
}

export function referenceColor (value: number, reference: number, root = '#ffffff'): string {
    if (reference === 0) {
        reference = 1;
    }
    const ratio = value / reference;
    return ratioColor(Math.abs(ratio), value >= 0, root);
}
