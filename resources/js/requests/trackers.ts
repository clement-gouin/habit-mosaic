import { Tracker } from '@interfaces';
import axios, { AxiosError } from 'axios';

export const ENDPOINT = '/trackers';

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

export async function createTracker (tracker: Tracker): Promise<Tracker> {
    return await axios.post(ENDPOINT, {
        ...tracker,
        category_id: tracker.category?.id
    })
        .then(resp => resp.data.data);
}

export async function updateTracker (tracker: Tracker): Promise<Tracker> {
    return await axios.put(`${ENDPOINT}/${tracker.id as number}`, {
        ...tracker,
        category_id: tracker.category?.id
    })
        .then(resp => resp.data.data);
}

export async function deleteTracker (tracker: Tracker): Promise<never> {
    return await axios.delete(`${ENDPOINT}/${tracker.id as number}`);
}
