import { Category, Tracker } from '@interfaces';
import axios from 'axios';

export const ENDPOINT = '/api/graph';

export async function getDayGraphData (days: number): Promise<[Array<number | null>, Array<number | null>]> {
    return await axios.get(`${ENDPOINT}/day?days=${days}`)
        .then(resp => [resp.data.data, resp.data.average]);
}

export async function getCategoryGraphData (category: Category, days: number): Promise<[Array<number | null>, Array<number | null>]> {
    return await axios.get(`${ENDPOINT}/categories/${category.id}?days=${days}`)
        .then(resp => [resp.data.data, resp.data.average]);
}

export async function getTrackerGraphData (tracker: Tracker, days: number): Promise<[Array<number | null>, Array<number | null>]> {
    return await axios.get(`${ENDPOINT}/trackers/${tracker.id}?days=${days}`)
        .then(resp => [resp.data.data, resp.data.average]);
}
