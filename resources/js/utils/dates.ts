export function formatShortDate (utc: string): string {
    return (new Date(Date.parse(utc))).toLocaleDateString('fr');
}

export function formatFullDate (utc: string): string {
    return (new Date(Date.parse(utc))).toLocaleString('fr');
}
