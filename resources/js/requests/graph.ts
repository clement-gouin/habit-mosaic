import { Category, Tracker } from '@interfaces';
import axios from 'axios';

export const ENDPOINT = '/api/graph';

export async function getDayGraphData (days?: number | undefined, months?: number | undefined): Promise<[Array<number | null>, number, number[]]> {
    if (days !== undefined) {
        return await axios.get(`${ENDPOINT}/day?days=${days}`)
            .then(resp => [resp.data.data, resp.data.starting_average, resp.data.months]);
    }
    return await axios.get(`${ENDPOINT}/day?months=${months ?? 1}`)
        .then(resp => [resp.data.data, resp.data.starting_average, resp.data.months]);
}

export async function getCategoryGraphData (category: Category, days?: number | undefined, months?: number | undefined): Promise<[Array<number | null>, number, number[]]> {
    if (days !== undefined) {
        return await axios.get(`${ENDPOINT}/categories/${category.id}?days=${days}`)
            .then(resp => [resp.data.data, resp.data.starting_average, resp.data.months]);
    }
    return await axios.get(`${ENDPOINT}/categories/${category.id}?months=${months ?? 1}`)
        .then(resp => [resp.data.data, resp.data.starting_average, resp.data.months]);
}

export async function getTrackerGraphData (tracker: Tracker, days?: number | undefined, months?: number | undefined): Promise<[Array<number | null>, number, number[]]> {
    if (days !== undefined) {
        return await axios.get(`${ENDPOINT}/trackers/${tracker.id}?days=${days}`)
            .then(resp => [resp.data.data, resp.data.starting_average, resp.data.months]);
    }
    return await axios.get(`${ENDPOINT}/trackers/${tracker.id}?months=${months ?? 1}`)
        .then(resp => [resp.data.data, resp.data.starting_average, resp.data.months]);
}
