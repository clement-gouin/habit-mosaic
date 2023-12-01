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
    unit: string
    value_step: number
    default_value: number
    target_value: number
    target_score: number
    data_point: DataPoint
    last_update?: string
}

export interface DataPoint extends Base {
    id: number
    date: string
    value: number
    tracker?: Tracker
}
