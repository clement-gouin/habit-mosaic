import { Category, DataPoint, TrackerFull } from '@interfaces';
import axios from 'axios';
import { saveURLParameters, toURLParameters } from '@utils/url';

export const ENDPOINT = '/api/table';

export async function getTableData (date: Date, days: number): Promise<[number, Category[], TrackerFull[], Record<string, DataPoint[]>]> {
    const params = { date: date.toISOString().split('T')[0], days };
    saveURLParameters(params);
    return await axios.get(`${ENDPOINT}?${toURLParameters(params)}`)
        .then(resp => [resp.data.average, resp.data.categories, resp.data.trackers, resp.data.data]);
}
