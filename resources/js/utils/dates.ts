export function formatDate (date: Date, withYear = false): string {
    return date.toLocaleDateString('en', { weekday: 'short', day: 'numeric', month: 'short', year: withYear ? 'numeric' : undefined });
}

export function isCurrentYear (date: Date): boolean {
    return date.getFullYear() === (new Date()).getFullYear();
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
