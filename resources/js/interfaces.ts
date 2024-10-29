import { TableCellFunction } from '@types';
export interface Base extends Record<string, unknown> {}

export interface BaseFormInput {
    id?: string
    name?: string
    label?: string
    placeholder?: string
    disabled?: boolean
    required?: boolean
    readonly?: boolean
    helpText?: string
    color?: string
    error?: string | boolean
}

export interface Alert {
    id: number
    type: AlertType
    title?: string
    text: string
    fade: boolean
}

export enum AlertType {
    Info = 'info',
    Success = 'success',
    Warning = 'warning',
    Error = 'error',
}

export interface QueryParameters extends Base {
    sortBy?: string
    search?: string
    perPage?: number
    page?: number
    filters?: Base
    include?: string[]
}

export interface TableColumn extends Base {
    id: string
    label?: string
    icon?: string
    title?: string
    cssClass?: TableCellFunction | string
    cssStyle?: TableCellFunction | string
    sortable?: boolean
    visible?: boolean
    clickable?: boolean
}

export interface Option<T> extends Base {
    key: string
    label: string | number
    value: T
    disabled?: boolean
}

export interface ErrorResponse extends Base {
    errors: Record<string, string[]>
}

export interface Statistics extends Base {
    total: number
    min: number
    average: number
    lower_quartile: number
    median: number
    upper_quartile: number
    max: number
}

export interface CategoryData extends Base {
    id?: number
    name: string
    icon?: string | null
}

export interface Category extends CategoryData {
    id: number
    order: number
}

export interface CategoryFull extends Category {
    statistics: Statistics
}

export interface TrackerData extends Base {
    id?: number
    category?: Category
    name: string
    icon: string
    unit?: string | null
    value_step: number
    target_value: number
    target_score: number
    single: boolean
    overflow: boolean
    stale_delay?: number | null
}

export interface Tracker extends TrackerData {
    id: number
    category: Category
    order: number
}

export interface TrackerFull extends Tracker {
    data_point: DataPoint
    staleness: number | null
    statistics: Statistics
}

export interface DataPoint extends Base {
    id: number
    tracker_id: number
    date: string
    value: number
    score: number
    updated_at: string
    tracker?: Tracker
    tableElement?: HTMLDivElement
}
