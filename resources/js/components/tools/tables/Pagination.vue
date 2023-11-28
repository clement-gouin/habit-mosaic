<template>
    <div>
        <div class="d-flex flex-row mt-3 justify-content-between">
            <div>
                <span>
                  <span>Showing <span class="font-medium">{{ firstItemIndex }}</span> to <span class="font-medium">{{ lastItemIndex }}</span> of <span class="font-medium">{{ total }}</span> results.</span><br>
                  <span>Number of results per page&nbsp;&nbsp;
                    <select
                        class="select-items btn btn-default btn-secondary"
                        @change="updateLimit"
                    >
                      <option
                          class="dropdown-item"
                          v-for="item in rowsPerPageOptions"
                          :key="item"
                          :selected="item === params.perPage"
                          :value="item"
                      >
                        {{ item }}
                      </option>
                    </select>
                  </span>
                </span>
            </div>
            <nav v-if="pageCount > 0">
                <ul class="pagination">
                    <li class="page-item" :class="isFirstPage ? 'disabled' : ''" @click.prevent="prevPage"><a class="page-link" href="#">&laquo;</a></li>
                    <template v-if="pageCount > maxPages && params.page >= 5">
                        <li class="page-item" @click.prevent="updatePage(1)"><a class="page-link" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">...</a></li>
                    </template>
                    <li v-for="page in shownPages" :key="page" class="page-item" :class="page === params.page ? 'active' : ''" @click.prevent="updatePage(page)">
                        <a class="page-link" href="#">{{page}}</a>
                    </li>
                    <template v-if="pageCount > maxPages && params.page <= pageCount - 4">
                        <li class="page-item disabled"><a class="page-link" href="#">...</a></li>
                        <li class="page-item" @click.prevent="updatePage(pageCount)"><a class="page-link" href="#">{{ pageCount }}</a></li>
                    </template>
                    <li class="page-item" :class="isLastPage ? 'disabled' : ''" @click.prevent="nextPage"><a class="page-link" href="#">&raquo;</a></li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { QueryParameters } from '@interfaces';

interface Props {
    /** total length of data (all pages) */
    total: number,
    /** query parameters */
    params: QueryParameters,
}

const props = defineProps<Props>();

const emit = defineEmits(['update']);

const rowsPerPageOptions = [10, 25, 50, 100];
const maxPages = 7;

const pageCount = computed(() => props.total === 0 ? 1 : Math.ceil(props.total / props.params.perPage));
const offset = computed(() => (props.params.page - 1) * props.params.perPage);
const isFirstPage = computed(() => props.params.page <= 1);
const firstItemIndex = computed(() => props.total === 0 ? 0 : offset.value + 1);
const isLastPage = computed(() => props.params.page >= pageCount.value);
const lastItemIndex = computed(() => isLastPage.value ? props.total : offset.value + props.params.perPage);

const shownPages = computed(() => {
    if (pageCount.value <= maxPages) {
        return Array(pageCount.value).fill(null)
            .map((_, i) => i + 1);
    }
    if (props.params.page < 5) {
        return Array(maxPages - 2).fill(null)
            .map((_, i) => i + 1);
    }
    if (props.params.page > pageCount.value - 4) {
        return Array(maxPages - 2).fill(null)
            .map((_, i) => pageCount.value - 4 + i);
    }
    return Array(maxPages - 4).fill(null)
        .map((_, i) => props.params.page - Math.floor((maxPages - 4) / 2) + i);
});

function updatePage (page: number) {
    emit('update', {
        ...props.params,
        ...{ page }
    });
}

function prevPage () {
    emit('update', {
        ...props.params,
        ...{ page: props.params.page - 1 }
    });
}

function nextPage () {
    emit('update', {
        ...props.params,
        ...{ page: props.params.page + 1 }
    });
}

function updateLimit (event: Event) {
    emit('update', {
        ...props.params,
        ...{
            page: 1,
            perPage: parseInt((event.target as HTMLInputElement).value)
        }
    });
}

</script>

<style scoped>
.page-item {
    user-select: none;
}

.page-item.disabled, .page-item.active {
    pointer-events: none;
}
</style>
