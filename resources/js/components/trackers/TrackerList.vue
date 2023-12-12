<template>
    <datatable :total="trackers.length" :data="trackers" :columns="columns" :with-pagination="false">
        <template #col-name="{row}">
            <i class="fa-xs " v-if="row.icon" :class="mapToClassName(row.icon)"></i>
            {{ row.name }}
        </template>
        <template #col-category="{row}">
            <template v-if="row.category">
                <i class="fa-xs " v-if="row.category.icon" :class="mapToClassName(row.category.icon)"></i>
                {{ row.category.name }}
            </template>
            <template v-else>(none)</template>
        </template>
    </datatable>
</template>

<script setup lang="ts">
import { mapToClassName } from '@utils/icons';
import Datatable from '@tools/tables/Datatable.vue';
import { TableColumn, Tracker } from '@interfaces';
import { ref } from 'vue';

interface Props {
    modelValue: Tracker[],
}

const props = defineProps<Props>();

const trackers = ref<Tracker[]>(props.modelValue);

const columns: TableColumn[] = [
    {
        id: 'name',
        label: 'Name'
    },
    {
        id: 'category',
        label: 'Category'
    },
    {
        id: 'actions',
        label: '',
        cssClass: 'text-left',
        cssStyle: 'width: 10em; vertical-align: middle'
    }
];

// function fetchData () {
//     listTrackers({})
//         .then(data => {
//             trackers.value = data;
//         });
// }

</script>

<style scoped>

</style>
