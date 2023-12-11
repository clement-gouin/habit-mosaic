import { TableCellFunction } from '@types';
export interface Base extends Record<string, unknown> {}

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
    label: string
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

export interface Tracker extends Base {
    id?: number
    name: string
    icon: string
    order: number
    unit: string
    value_step: number
    target_value: number
    target_score: number
    single: boolean
    data_point: DataPoint
}

export interface DataPoint extends Base {
    id: number
    date: string
    value: number
    score: number
    tracker?: Tracker
}
