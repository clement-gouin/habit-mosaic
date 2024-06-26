<template>
    <loading-mask v-if="loading" />
    <datatable style="height: 100vh" overflow :total="tableData.length" :data="tableData" :columns="columns" :with-pagination="false">
        <template #col-date="{value}">
            <small class="font-monospace">{{ formatDate(value as Date) }}</small>
        </template>
        <template #col-score="{value}">
            {{ (value as number).toFixed(1) }}
        </template>
        <template v-for="slot in slots" v-bind:key="slot.id" v-slot:[slot.id]="{ value }" >
            <div>
                <input v-if="(slot.tracker as Tracker).single && (!(slot.tracker as Tracker).overflow || (value as DataPoint).value < (slot.tracker as Tracker).target_value)" type="checkbox" :checked="(value as DataPoint).value >= (slot.tracker as Tracker).target_value" @input="changedSingle(slot.tracker, value, $event.target.checked)">
                <div :ref="(el) => (value as DataPoint).tableElement = el" v-else contenteditable @focusin="selectAll" @blur="changed(slot.tracker, value, $event.target)" @keydown="keyDown(slot.tracker, value, $event)">
                    {{ (value as DataPoint).value.toFixed(precision(slot.tracker.value_step)) }}
                </div>
            </div>
        </template>
    </datatable>
</template>

<script setup lang="ts">
import { Category, DataPoint, Statistics, TableColumn, Tracker, TrackerFull } from '@interfaces';
import { computed, onMounted, ref } from 'vue';
import Datatable from '@tools/tables/Datatable.vue';
import { updateDataPoint } from '@requests/dataPoints';
import { round } from '@popperjs/core/lib/utils/math';
import { precision } from '@utils/numbers';
import { getTableData } from '@requests/table';
import { referenceColor } from '@utils/colors';
import { formatDate } from '@utils/dates';
import { useBackgroundFetch } from '@composables/useBackgroundFetch';
import LoadingMask from '@tools/LoadingMask.vue';

interface Props {
    date: string,
    days: number,
    statistics: Statistics
    categories: Category[],
    trackers: TrackerFull[],
}

const props = defineProps<Props>();

const date = ref<number>(Date.parse(props.date));
const days = ref<number>(props.days);
const statistics = ref<Statistics>(props.statistics);
const categories = ref(props.categories);
const trackers = ref(props.trackers);
const loading = ref(true);
const tableData = ref<Record<string, unknown>[]>([]);

const slots = computed<{id: string, tracker: Tracker}[]>(() => trackers.value.map(tracker => {
    return {
        id: `col-tracker-${tracker.id}`,
        tracker
    };
}));

const color = (tracker: Tracker, value: number, variable: string) => referenceColor(Math.sign(tracker.target_score) * value, tracker.target_value, variable);
const colorDay = (value: number, variable: string) => referenceColor(value, statistics.value.average, variable);

const { loading: loadingInternal } = useBackgroundFetch(async () => getTableData(new Date(date.value), days.value), ([newStatistics, newCategories, newTrackers, newData]) => {
    statistics.value = newStatistics;
    categories.value = newCategories;
    trackers.value = newTrackers;
    tableData.value = computeData(newData);
    recomputeScore();
    loading.value = false;
});

const columns = computed<TableColumn[]>(() => {
    return [
        {
            id: 'date',
            label: 'Date',
            icon: '',
            title: '',
            cssStyle: row => {
                const base = 'position: sticky;left: 0;z-index:50;';
                if (row) {
                    return base + `background-color: ${colorDay(row.score, 'bg-subtle')}; color: ${colorDay(row.score, 'text-emphasis')}`;
                }
                return base;
            },
            cssClass: 'align-middle w-fit text-center'
        },
        {
            id: 'score',
            label: 'Score',
            icon: '',
            title: '',
            cssStyle: row => {
                if (row) {
                    return `background-color: ${colorDay(row.score, 'bg-subtle')}; color: ${colorDay(row.score, 'text-emphasis')}`;
                }
            },
            cssClass: 'align-middle w-fit text-center'
        },
        ...sortTrackers(trackers.value).map(tracker => {
            return {
                id: `tracker-${tracker.id}`,
                label: '',
                icon: tracker.icon,
                title: tracker.name,
                cssStyle: row => {
                    const base = 'min-width:3em;line-height: 3em;font-size:.9em;';
                    if (row) {
                        return base + `background-color: ${color(tracker, row[`tracker-${tracker.id}`].value, 'bg-subtle')}; color: ${color(tracker, row[`tracker-${tracker.id}`].value, 'text-emphasis')}`;
                    }
                    return base;
                },
                cssClass: 'align-middle text-center align-middle p-0'
            };
        })
    ];
});

function sortTrackers (data: Tracker[]) {
    return data
        .sort((a, b) => a.category.order === b.category.order ? b.order - a.order : b.category.order - a.category.order)
        .reverse();
}

function computeData (data: Record<string, DataPoint[]>): Record<string, unknown>[] {
    return Object.keys(data)
        .map((date: string) => {
            const row: Record<string, unknown> = {
                date: new Date(Date.parse(date)),
                score: 0
            };
            const currentRow: Record<string, unknown>|undefined = tableData.value.find(r => r.date?.toISOString().substring(0, 10) === date);
            data[date].forEach(dataPoint => {
                const tracker = trackers.value.find(tracker => tracker.id === dataPoint.tracker_id);
                if (tracker) {
                    const currentDataPoint: DataPoint|undefined = currentRow ? currentRow[`tracker-${tracker.id}`] as DataPoint : undefined;
                    if (currentDataPoint?.id === dataPoint.id && Date.parse(currentDataPoint.updated_at) > Date.parse(dataPoint.updated_at)) {
                        dataPoint = currentDataPoint;
                    }
                    dataPoint.tracker = tracker;
                    dataPoint.score = tracker.target_score * dataPoint.value / tracker.target_value;
                    row[`tracker-${tracker.id}`] = dataPoint;
                }
            });
            return row;
        });
}

function recomputeScore () {
    tableData.value.forEach(row => {
        row.score = trackers.value.map(tracker => (row[`tracker-${tracker.id}`] as DataPoint).score).reduce((a, b) => a + b, 0);
    });
}

function changedSingle (tracker: Tracker, dataPoint: DataPoint, value: boolean) {
    update(tracker, dataPoint, value ? 1 : 0);
}

function changed (tracker: Tracker, dataPoint: DataPoint, target: HTMLDivElement) {
    const text = target.innerText.replace(/\s/gu, '');
    if (!text.match(/^\d+(\.\d+)?$/)) {
        target.innerText = `${dataPoint.value}`;
    } else {
        const value = round(parseFloat(text) / tracker.value_step) * tracker.value_step;
        update(tracker, dataPoint, value);
        target.innerText = `${value.toFixed(precision(tracker.value_step))}`;
    }
}

function keyDown (tracker: Tracker, dataPoint: DataPoint, event: KeyboardEvent) {
    if (event.key === 'Enter') {
        (event.target as HTMLDivElement).blur();
        let next = false;
        (event.shiftKey ? tableData.value.toReversed() : tableData.value).forEach(row => {
            if (next) {
                const nextDataPoint = (row[`tracker-${tracker.id}`] as DataPoint);
                nextDataPoint.tableElement?.focus();
                next = false;
            } else if ((row[`tracker-${tracker.id}`] as DataPoint).id === dataPoint.id) {
                next = true;
            }
        });
        event.preventDefault();
    }
}

function update (tracker: Tracker, dataPoint: DataPoint, value: number) {
    dataPoint.value = value;
    dataPoint.updated_at = (new Date()).toISOString();
    dataPoint.score = tracker.target_score * dataPoint.value / tracker.target_value;
    recomputeScore();
    loadingInternal.value = true;
    updateDataPoint(dataPoint)
        .finally(() => {
            loadingInternal.value = false;
        });
}

function selectAll (event: FocusEvent) {
    if (event.target) {
        window.getSelection()?.selectAllChildren(event.target);
    }
}

onMounted(recomputeScore);
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>
