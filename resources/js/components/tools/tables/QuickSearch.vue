<template>
    <div class="form-group form-group-sm input-group input-group-sm">
        <input type="text" v-model.lazy="search" class="form-control" placeholder="Search...">
        <span class="input-group-btn">
      <button @click="clear()" class="btn btn-default btn-secondary rounded-start-0" type="button"><i class="bi-x" /></button>
    </span>
    </div>
</template>

<script lang="ts" setup>
import { ref, watch } from 'vue';
import { QueryParameters } from '@interfaces';

const search = ref<string>('');

interface Props {
    /** query parameters */
    params: QueryParameters,
}

const props = defineProps<Props>();

const emit = defineEmits(['update']);

function clear () {
    search.value = '';
    emit('update', {
        ...props.params,
        ...{
            page: 1,
            search: ''
        }
    });
}

watch(search, () => {
    emit('update', {
        ...props.params,
        ...{
            page: 1,
            search: search.value ?? ''
        }
    });
});

watch(props, () => {
    search.value = props.params.search ?? '';
});
</script>
