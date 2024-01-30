import { CategoryFull, Statistics, TrackerFull } from '@interfaces';
import axios from 'axios';
import { saveURLParameters, toURLParameters } from '@utils/url';

export const ENDPOINT = '/api/day';

export async function getDayData (date: Date): Promise<[string, Statistics, CategoryFull[], TrackerFull[]]> {
    const params = { date: date.toISOString().split('T')[0] };
    saveURLParameters(params);
    return await axios.get(`${ENDPOINT}?${toURLParameters(params)}`)
        .then(resp => [resp.data.date, resp.data.statistics, resp.data.categories, resp.data.trackers]);
}
