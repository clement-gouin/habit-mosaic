<template>
    <div class="table-responsive position-relative" :class="{overflow: overflow}">
        <div v-if="$slots.toolbar || withSearch">
            <div class="d-flex flex-row mb-3 justify-content-between">
                <div>
                    <slot name="toolbar"></slot>
                </div>
                <template v-if="withSearch">
                    <div>
                        <QuickSearch
                            :params="params"
                            @update="updateParams"
                        ></QuickSearch>
                    </div>
                </template>
            </div>
        </div>
        <table class="table table-bordered table-hover rounded-5" style="table-layout: fixed" cellspacing="0" cellpadding="0" border="0" :class="{'table-striped': !withSubRows}" >
            <thead>
            <tr>
                <th v-for="col in filteredColumns" :key="col.id"
                    :class="typeof col.cssClass === 'function' ? col.cssClass(null) : col.cssClass"
                    :style="typeof col.cssStyle === 'function' ? col.cssStyle(null) : col.cssStyle"
                    :title="col.title"
                >
                    <a v-if="col.sortable" href="#"
                       class="text-decoration-none"
                       @click.prevent="toggleSort(col.id)"
                    >
                        <span class="text-body"><slot :name="'head-' + col.id" :col="col"><i v-if="col.icon" :class="mapToClassName(col.icon)"></i>{{ col.label }}</slot></span>
                        <span class="caret-sort" :class="(descending ? 'caret-down' : 'caret-up')"
                              v-if="params.sortBy?.endsWith(col.id)"></span>
                        <span class="caret-sort caret-up disabled" v-else></span>
                    </a>
                    <span v-else><slot :name="'head-' + col.id" :col="col"><i v-if="col.icon" :class="mapToClassName(col.icon)"></i>{{ col.label }}</slot></span>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="data.length === 0 && !loading">
                <td class="text-center" :colspan='columns.length'>No data</td>
            </tr>
            <template v-else v-for="(row, index) in data" :key="index">
                <tr>
                    <td v-for="col in filteredColumns" :key="col.id"
                        :class="typeof col.cssClass === 'function' ? col.cssClass(row) : col.cssClass"
                        :style="`cursor: ${withSubRows && col.clickable ? 'pointer': 'inherit'};` + (typeof col.cssStyle === 'function' ? col.cssStyle(row) : col.cssStyle)"
                        :data-bs-toggle="withSubRows && col.clickable ? 'collapse' : ''"
                        :href="withSubRows && col.clickable ? `#subrow-${index}` : ''"
                        :aria-controls="withSubRows && col.clickable ? `subrow-${index}` : ''"
                        :title="col.title"
                    >
                        <slot :name="'col-' + col.id" :row="row" :value="row[col.id] ?? undefined" :index="index">{{ row[col.id] ?? '' }}</slot>
                    </td>
                </tr>
                <tr v-if="withSubRows" class="subrow p-0">
                    <td :colspan="filteredColumns.length" class="p-0">
                        <div :id="`subrow-${index}`" class="collapse in p-3">
                            <slot name="subrow" :row="row" :value="undefined" :index="index" />
                        </div>
                    </td>
                </tr>
            </template>
            </tbody>
            <div class="loading" v-if="loading">
                <div class="spinner-border border-5" style="margin: 1em" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </table>

        <template v-if="withPagination">
            <Pagination
                :total="total"
                :params="params"
                @update="updateParams"
            ></Pagination>
        </template>
    </div>
</template>

<script lang="ts" setup>
import { computed, onBeforeMount, onMounted, ref } from 'vue';
import { Base, QueryParameters, TableColumn } from '@interfaces';
import Pagination from './Pagination.vue';
import QuickSearch from './QuickSearch.vue';
import { mapToClassName } from '@utils/icons';

interface Props {
    columns: TableColumn[]
    data: Base[]
    total: number
    defaultParams?: QueryParameters
    withSearch?: boolean
    withPagination?: boolean
    autoFetch?: boolean
    withSubRows?: boolean
    loading?: boolean
    overflow?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    withSearch: false,
    withPagination: true
});

interface Emits {
    (e: 'fetch', params: QueryParameters): void
}

const emit = defineEmits<Emits>();

const params = ref<QueryParameters>({});

const descending = ref<boolean>(false);

const filteredColumns = computed(() => props.columns.filter(column => (
    column.visible === undefined || column.visible
)));

function updateParams (newParams: QueryParameters) {
    params.value = newParams;
    emit('fetch', params.value);
}

function toggleSort (field: string) {
    descending.value = !descending.value;

    updateParams({
        ...params.value,
        sortBy: (descending.value ? '-' : '') + field
    });
}

onBeforeMount(() => {
    params.value = {
        page: 1,
        perPage: 10,
        ...props.defaultParams
    };
    descending.value = params.value.sortBy?.startsWith('-') ?? false;
});

onMounted(() => {
    if (props.autoFetch) {
        emit('fetch', params.value);
    }
});

defineExpose({ updateParams });
</script>

<style scoped lang="scss">
.table {
    font-size: inherit;
    position: relative;

    th {
        vertical-align: middle;

        > a, > a:focus, > a:hover {
            text-decoration: none;
            color: inherit;
            display: inline-block;
            white-space: nowrap;
            width: 100%;
        }

        > a .text-body {
            white-space: normal;
        }
    }

    .loading {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        background: white;
        opacity: 0.8;

        .spinner-border {
            position: absolute;
            top: 50%;
            left: 50%;
        }
    }
}

.caret-sort {
    display: inline-block;
    width: 0;
    height: 0;
    vertical-align: middle;
    border-right: 4px solid transparent;
    border-left: 4px solid transparent;
    margin: 10px 0 10px 5px;

    &.caret-up {
        border-bottom: 4px dashed;
    }

    &.caret-down {
        border-top: 4px dashed;
    }

    &.disabled {
        color: rgb(204, 204, 204);
    }
}

.table-hover > tbody > tr.subrow:hover > * {
    --bs-table-color-state: var(--bs-table-color);
    --bs-table-bg-state: var(--bs-gray-100);
}

.table-hover > tbody > tr.subrow > * {
    --bs-table-bg-state: var(--bs-gray-100);
}

.overflow {
    overflow: auto;
}

.overflow .table {
    width: max-content;
}

.overflow thead{
    position: sticky;
    top: 0;
    z-index: 100;
}
</style>
