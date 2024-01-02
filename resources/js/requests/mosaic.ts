import { Category, Tracker } from '@interfaces';
import axios from 'axios';

export const ENDPOINT = '/api/mosaic';

export async function getDayMosaicData (days: number): Promise<Array<number | null>> {
    return await axios.get(`${ENDPOINT}/day?days=${days}`)
        .then(resp => resp.data);
}

export async function getCategoryMosaicData (category: Category, days: number): Promise<Array<number | null>> {
    return await axios.get(`${ENDPOINT}/categories/${category.id}?days=${days}`)
        .then(resp => resp.data);
}

export async function getTrackerMosaicData (tracker: Tracker, days: number): Promise<Array<number | null>> {
    return await axios.get(`${ENDPOINT}/trackers/${tracker.id}?days=${days}`)
        .then(resp => resp.data);
}
