<template>
    <loading-mask v-if="loading" />
    <datatable style="height: 100vh" overflow :total="tableData.length" :data="tableData" :columns="columns" no-pagination>
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
import { backgroundColor, referenceColor, textColor } from '@utils/colors';
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

const baseColor = (tracker: Tracker, value: number) => referenceColor(Math.sign(tracker.target_score) * value, tracker.target_value);
const baseColorDay = (value: number) => referenceColor(value, statistics.value.average);

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
            cssStyle: (row: {score: number}|null) => {
                if (row) {
                    return `z-index:50;background-color: ${backgroundColor(baseColorDay(row.score))}; color: ${textColor(baseColorDay(row.score))}`;
                }
                return 'z-index: 200;';
            },
            cssClass: 'sticky align-middle left-0 w-fit text-center'
        },
        {
            id: 'score',
            label: 'Score',
            icon: '',
            title: '',
            cssStyle: (row: {score: number}|null) => {
                if (row) {
                    return `background-color: ${backgroundColor(baseColorDay(row.score))}; color: ${textColor(baseColorDay(row.score))}`;
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
                cssStyle: (row: Record<string, { value: number }>|null) => {
                    const base = 'min-width:3em;line-height:3em;font-size:.9em;';
                    if (row) {
                        return base + `background-color: ${backgroundColor(baseColor(tracker, row[`tracker-${tracker.id}`].value))}; color: ${textColor(baseColor(tracker, row[`tracker-${tracker.id}`].value))}`;
                    }
                    return base;
                },
                cssClass: 'align-middle text-center p-0'
            };
        })
    ] as TableColumn[];
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
        window.getSelection()?.selectAllChildren(event.target as HTMLElement);
    }
}

onMounted(recomputeScore);
</script>

<script lang="ts">
export default { inheritAttrs: false };
</script>
