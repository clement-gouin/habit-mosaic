import { Category, Tracker } from '@interfaces';
import axios from 'axios';
import { saveURLParameters, toURLParameters } from '@utils/url';

export const ENDPOINT = '/dashboard';

export async function getDashboardData (date: Date): Promise<[number, Category[], Tracker[]]> {
    const params = { date: date.toISOString() };
    saveURLParameters(params);
    return await axios.get(`${ENDPOINT}/data?${toURLParameters(params)}`)
        .then(resp => [resp.data.date, resp.data.categories, resp.data.trackers]);
}
