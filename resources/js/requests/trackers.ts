import { Tracker, TrackerData } from '@interfaces';
import axios, { AxiosError } from 'axios';
import { useAlertsStore } from '@stores/alerts';

export const ENDPOINT = '/api/trackers';

const { alertSuccess } = useAlertsStore();

let controller: AbortController;

export async function listTrackers (): Promise<Tracker[]> {
    const url = `${ENDPOINT}/list`;

    controller?.abort();

    controller = new AbortController();

    try {
        return await axios.get(url)
            .then(resp => resp.data.data as Tracker[]);
    } catch (error) {
        if ((error as AxiosError).message === 'canceled') {
            return [];
        }
        throw error;
    }
}

export async function createTracker (tracker: TrackerData): Promise<Tracker> {
    return await axios.post(ENDPOINT, {
        ...tracker,
        category_id: tracker.category.id
    })
        .then(resp => {
            alertSuccess('Tracker created');
            return resp.data.data;
        });
}

export async function updateTracker (tracker: TrackerData): Promise<Tracker> {
    return await axios.put(`${ENDPOINT}/${tracker.id as number}`, {
        ...tracker,
        category_id: tracker.category.id
    })
        .then(resp => {
            alertSuccess('Tracker updated');
            return resp.data.data;
        });
}

export async function deleteTracker (tracker: TrackerData): Promise<void> {
    await axios.delete(`${ENDPOINT}/${tracker.id as number}`)
        .then(resp => {
            alertSuccess('Tracker deleted');
            return resp.data.data;
        });
}
