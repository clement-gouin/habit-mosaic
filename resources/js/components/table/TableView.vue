<template>
    <datatable overflow :total="tableData.length" :data="tableData" :columns="columns" :with-pagination="false" :loading="loading">
        <template #col-date="{value}">
            <small class="font-monospace">{{ (value as Date).toLocaleDateString('en', { weekday: 'short', day: 'numeric', month: 'short' }) }}</small>
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
import { Category, DataPoint, TableColumn, Tracker, TrackerFull } from '@interfaces';
import { computed, onMounted, ref } from 'vue';
import Datatable from '@tools/tables/Datatable.vue';
import { updateDataPoint } from '@requests/dataPoints';
import { round } from '@popperjs/core/lib/utils/math';
import { precision } from '@utils/numbers';

interface Props {
    categories: Category[],
    trackers: TrackerFull[],
    data: Record<number, DataPoint[]>
}

const props = defineProps<Props>();

const loading = ref(false);

const slots = computed<{id: string, tracker: Tracker}[]>(() => props.trackers.map(tracker => {
    return {
        id: `col-tracker-${tracker.id}`,
        tracker
    };
}));

const columns = computed<TableColumn[]>(() => {
    return [
        {
            id: 'date',
            label: 'Date',
            icon: '',
            title: '',
            cssClass: 'align-middle w-fit text-center'
        },
        {
            id: 'score',
            label: 'Score',
            icon: '',
            title: '',
            cssClass: 'align-middle w-fit text-center'
        },
        ...sortTrackers(props.trackers).map(tracker => {
            return {
                id: `tracker-${tracker.id}`,
                label: '',
                icon: tracker.icon,
                title: tracker.name,
                cssStyle: 'min-width:3em;line-height:2.5em;font-size:.9em',
                cssClass: 'align-middle text-center align-middle p-0'
            };
        })
    ];
});

const tableData = ref<Record<string, unknown>[]>(computeData());

function sortTrackers (data: Tracker[]) {
    return data
        .sort((a, b) => (a.category?.order ?? 0) === (b.category?.order ?? 0) ? b.order - a.order : (b.category?.order ?? 0) - (a.category?.order ?? 0))
        .reverse();
}

function computeData (): Record<string, unknown>[] {
    return Object.keys(props.data).reverse()
        .map((timestamp: number) => {
            const row: Record<string, unknown> = {
                date: new Date(timestamp * 1000),
                score: 0
            };
            props.data[timestamp].forEach(dataPoint => {
                const tracker = props.trackers.find(tracker => tracker.id === dataPoint.tracker_id);
                if (tracker) {
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
        row.score = props.trackers.map(tracker => (row[`tracker-${tracker.id}`] as DataPoint).score).reduce((a, b) => a + b, 0);
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
    dataPoint.score = tracker.target_score * dataPoint.value / tracker.target_value;
    recomputeScore();
    updateDataPoint(dataPoint);
}

function selectAll (event: FocusEvent) {
    if (event.target) {
        window.getSelection()?.selectAllChildren(event.target);
    }
}

onMounted(recomputeScore);
</script>
