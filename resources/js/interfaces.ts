import { TableCellFunction } from '@types';
export interface Base extends Record<string, unknown> {}

export interface Alert {
    id: number
    type: string
    title?: string
    text: string
    show: boolean
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

export interface Option extends Base {
    key: string | number
    label: string | number
    value: unknown
    disabled?: boolean
}

export interface ErrorResponse extends Base {
    errors: Record<string, string[]>
}

export interface CategoryData extends Base {
    id?: number
    name: string
    icon?: string
}

export interface Category extends CategoryData {
    id: number
    order: number
}

export interface CategoryFull extends Category {
    average: number
}

export interface TrackerData extends Base {
    id?: number
    category?: Category
    name: string
    icon: string
    unit?: string
    value_step: number
    target_value: number
    target_score: number
    single: boolean
    overflow: boolean
}

export interface Tracker extends TrackerData {
    id: number
    order: number
}

export interface TrackerFull extends Tracker {
    data_point: DataPoint
    average: number
}

export interface DataPoint extends Base {
    id: number
    tracker_id: number
    date: string
    value: number
    score: number
    tracker?: Tracker
    tableElement?: HTMLDivElement
}
