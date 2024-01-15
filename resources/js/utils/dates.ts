export function formatShortDate (utc: string): string {
    return (new Date(Date.parse(utc))).toLocaleDateString('fr');
}

export function formatFullDate (utc: string): string {
    return (new Date(Date.parse(utc))).toLocaleString('fr');
}

export function addDays (date: Date | number, days: number): Date {
    const result = new Date(date);
    result.setDate(result.getDate() + days);
    return result;
}
