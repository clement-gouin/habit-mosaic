<template>
    <div class="join" v-if="pageCount > 0">
        <button class="join-item btn" :class="isFirstPage ? 'btn-disabled' : ''" @click="prevPage">&laquo;</button>
        <template v-if="pageCount > maxPages && page >= 5">
            <button class="join-item btn" @click="updatePage(1)">1</button>
            <button class="join-item btn btn-disabled">...</button>
        </template>
        <button v-for="pageIndex in shownPages" :key="pageIndex" class="join-item btn" :class="pageIndex === page ? 'btn-active' : ''" @click="updatePage(pageIndex)">{{ pageIndex }}</button>
        <template v-if="pageCount > maxPages && page <= pageCount - 4">
            <button class="join-item btn btn-disabled">...</button>
            <button class="join-item btn" @click="updatePage(pageCount)">{{ pageCount }}</button>
        </template>
        <button class="join-item btn" :class="isLastPage ? 'btn-disabled' : ''" @click="nextPage">&raquo;</button>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

interface Props {
    total: number
    perPage: number
}

const props = defineProps<Props>();
const page = defineModel<number>({ required: true, default: 1 });

interface Events {
    (e: 'change', params: number): void;
}

const emit = defineEmits<Events>();

const maxPages = 7;

const pageCount = computed(() => props.total === 0 ? 1 : Math.ceil(props.total / props.perPage));
const isFirstPage = computed(() => page.value <= 1);
const isLastPage = computed(() => page.value >= pageCount.value);

const shownPages = computed(() => {
    if (pageCount.value <= maxPages) {
        return Array(pageCount.value).fill(null)
            .map((_, i) => i + 1);
    }
    if (page.value < 5) {
        return Array(maxPages - 2).fill(null)
            .map((_, i) => i + 1);
    }
    if (page.value > pageCount.value - 4) {
        return Array(maxPages - 2).fill(null)
            .map((_, i) => pageCount.value - 4 + i);
    }
    return Array(maxPages - 4).fill(null)
        .map((_, i) => page.value - Math.floor((maxPages - 4) / 2) + i);
});

function updatePage (pageNumber: number) {
    page.value = pageNumber;

    emit('change', page.value);
}

function prevPage () {
    updatePage(page.value - 1);
}

function nextPage () {
    updatePage(page.value + 1);
}

</script>
