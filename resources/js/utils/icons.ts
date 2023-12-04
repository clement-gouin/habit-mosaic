import levenshtein from 'js-levenshtein';

export function getIconList (): string[] {
    return __ICONS__;
}

export function searchIcon (search: string | undefined): string[] {
    if (search === undefined || search.length <= 1) {
        return __ICONS__;
    }

    const output = __ICONS__.filter((iconName: string) => {
        if (iconName.toLowerCase().includes(search.toLowerCase())) {
            return true;
        }
        if (iconName in __ICON_SEARCHES__) {
            for (const term: string of __ICON_SEARCHES__[iconName]) {
                if (term.toLowerCase().includes(search.toLowerCase()) as boolean) {
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
    const style: string = __ICON_STYLES__[iconName][0];
    return `fa-${style} fa-${iconName}`;
}
