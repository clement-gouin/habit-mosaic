import { TableCellFunction } from '@types';
export interface Base extends Record<string, unknown> {}

export interface QueryParameters extends Base {
    /** field to sort */
    sortBy?: string
    /** string to search  */
    search?: string
    /** number of items per page */
    perPage?: number
    /** page number */
    page?: number
    filters?: Base
    include?: string[]
}

export interface TableColumn extends Base {
    /** field name of column */
    id: string
    /** column title to display */
    label: string
    /** CSS class to apply to each cell */
    cssClass?: TableCellFunction | string
    /** CSS style to apply to each cell */
    cssStyle?: TableCellFunction | string
    /** can sort column */
    sortable?: boolean
    visible?: boolean
    clickable?: boolean
}

export interface Option extends Base {
    key: string | number
    label: string | number
    value: unknown
    disabled?: boolean
}

export interface ErrorResponse extends Base {
    /** fields and their associated errors */
    errors: Record<string, string[]>
}
