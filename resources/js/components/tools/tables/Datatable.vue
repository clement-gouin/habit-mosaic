<template>
    <div class="overflow-x-scroll relative" :class="overflow ? 'overflow' : ''">
        <table class="table bg-white" :class="stripped ? 'table-zebra ' : ''">
            <thead>
            <tr>
                <th v-for="col in columns" :key="col.id"
                    :title="col.title"
                    :class="typeof col.cssClass === 'function' ? col.cssClass(null, null) : col.cssClass"
                    :style="typeof col.cssStyle === 'function' ? col.cssStyle(null, null) : col.cssStyle"
                >
                    <a v-if="col.sortable" href="#"
                       class="group"
                       @click.prevent="toggleSort(col.id)"
                    >
                        <slot :name="'head-' + col.id">{{ col.label }}</slot>
                        <template v-if="params.sortBy?.endsWith(col.id)">
                            <i v-if="descending" class="w-4 ml-2 inline-block text-gray-800 fas fa-caret-down" />
                            <i v-else class="w-4 ml-2 inline-block text-gray-800 fas fa-caret-down" />
                        </template>
                        <i v-else class="w-4 ml-2 inline-block opacity-0 group-hover:opacity-100 text-gray-400 fas fa-caret-down" />
                    </a>
                    <template v-else>
                        <slot :name="'head-' + col.id"><i v-if="col.icon" :class="mapToClassName(col.icon)"></i>{{ col.label }}</slot>
                    </template>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr :class="hover ? 'hover' : ''" v-for="(row, index) in data" v-bind:key="`item-${index}`">
                <td v-for="col in columns" :key="col.id"
                    :class="typeof col.cssClass === 'function' ? col.cssClass(row, index) : col.cssClass"
                    :style="typeof col.cssStyle === 'function' ? col.cssStyle(row, index) : col.cssStyle"
                >
                    <slot :name="'col-' + col.id" :row="row" :value="row[col.id] ?? undefined" :index="index">{{ row[col.id] ?? '' }}</slot>
                </td>
            </tr>
            </tbody>
        </table>
        <div v-if="!noPagination" class="w-full flex justify-center">
            <Pagination
                :total="total"
                v-model="params.page"
                :per-page="params.perPage"
                @change="fetchData"
            ></Pagination>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { Base, QueryParameters, TableColumn } from '@interfaces';
import Pagination from '@tools/tables/Pagination.vue';
import { onMounted, ref } from 'vue';
import { mapToClassName } from '@utils/icons';

interface Props {
    columns: TableColumn[]
    data: Base[]
    total: number
    noPagination?: boolean
    autoFetch?: boolean
    stripped?: boolean
    hover?: boolean
    overflow?: boolean
}

const props = defineProps<Props>();

const params = defineModel<QueryParameters>('params', {
    default: {
        page: 1,
        perPage: 25
    }
});

const descending = ref<boolean>(params.value.sortBy?.startsWith('-') ?? false);

interface Emits {
    (e: 'fetch', params: QueryParameters): void | Promise<void>;
}

const emit = defineEmits<Emits>();

function fetchData () {
    emit('fetch', params.value);
}

function toggleSort (field: string) {
    if (params.value.sortBy?.endsWith(field)) {
        descending.value = !descending.value;
    } else {
        descending.value = false;
    }
    params.value.sortBy = (descending.value ? '-' : '') + field;
    params.value.page = 1;
    fetchData();
}

onMounted(() => {
    if (props.autoFetch) {
        fetchData();
    }
});
</script>

<style scoped>
.overflow {
    overflow: auto;
}

.overflow .table {
    width: max-content;
}

.overflow th{
    position: sticky;
    top: 0 !important;
    z-index: 100;
    background-color: white;
}
</style>
