import info from '@fortawesome/fontawesome-free/metadata/icon-families.json';

export function getIconList (): string[] {
    return Object.keys(info);
}

export function searchIcon (search: string): string[] {
    if (!search) {
        return getIconList();
    }
    return getIconList().filter((iconName: string) => {
        if (iconName.toLowerCase().includes(search.toLowerCase())) {
            return true;
        }
        for (const term: string of info[iconName].search.terms ?? []) {
            if (term.toLowerCase().includes(search.toLowerCase())) {
                return true;
            }
        }
        for (const term: string of (info[iconName].aliases ?? []).names ?? []) {
            if (term.toLowerCase().includes(search.toLowerCase())) {
                return true;
            }
        }
    });
}

export function mapToClassName (iconName: string): string {
    const style = info[iconName].familyStylesByLicense.free[0].style;

    return `fa-${style} fa-${iconName}`;
}
