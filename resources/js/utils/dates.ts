export function formatShortDate (utc: string): string {
    return (new Date(Date.parse(utc))).toLocaleDateString('fr');
}

export function formatFullDate (utc: string): string {
    return (new Date(Date.parse(utc))).toLocaleString('fr');
}

export function formatISODate (date: Date): string {
    return date.toISOString().split('T')[0];
}

export function dateEquals (date1: Date, date2: Date): boolean {
    return formatISODate(date1) === formatISODate(date2);
}

export function addDays (date: Date | number, days: number): Date {
    const result = new Date(date);
    result.setDate(result.getDate() + days);
    return result;
}
