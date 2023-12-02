import { QueryParameters, Tracker } from '@interfaces';
import axios, { AxiosError } from 'axios';
import { toURLParameters } from '@utils/url';

export const ENDPOINT = '/trackers';

let controller: AbortController;

export async function listTrackers (params: QueryParameters): Promise<[Tracker[], number]> {
    const url = `${ENDPOINT}/list?${toURLParameters(params)}`;

    controller?.abort();

    controller = new AbortController();

    try {
        return await axios.get(url)
            .then(resp => [resp.data.data as Tracker[], resp.data.meta?.total]);
    } catch (error) {
        if ((error as AxiosError).message === 'canceled') {
            return [[], 0];
        }
        throw error;
    }
}

export async function createTracker (tracker: Tracker): Promise<Tracker> {
    return await axios.post(ENDPOINT, tracker)
        .then(resp => resp.data.data);
}

export async function updateTracker (tracker: Tracker): Promise<Tracker> {
    return await axios.put(`${ENDPOINT}/${tracker.id as number}`, tracker)
        .then(resp => resp.data.data);
}

export async function deleteTracker (tracker: Tracker): Promise<never> {
    return await axios.delete(`${ENDPOINT}/${tracker.id as number}`);
}
