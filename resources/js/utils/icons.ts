import levenshtein from 'js-levenshtein';

export function getIconList (): string[] {
    return __ICONS__;
}

export function searchIcon (search: string|null): string[] {
    if (!search || search.length <= 1) {
        return __ICONS__;
    }

    const output = __ICONS__.filter((iconName: string) => {
        if (iconName.toLowerCase().includes(search.toLowerCase())) {
            return true;
        }
        if (__ICON_SEARCHES__hasOwnProperty('iconName')) {
            for (const term: string of __ICON_SEARCHES__[iconName]) {
                if (term.toLowerCase().includes(search.toLowerCase())) {
                    return true;
                }
            }
        }
        return false;
    });

    if (output.length === 0) {
        output.push(...__ICONS__);
        output.sort((a, b) => levenshtein(a, search) - levenshtein(b, search));
        return output.slice(0, 20);
    }

    output.sort((a, b) => levenshtein(a, search) - levenshtein(b, search));

    return output;
}

export function mapToClassName (iconName: string): string {
    return `fa-${__ICON_STYLES__[iconName][0]} fa-${iconName}`;
}
