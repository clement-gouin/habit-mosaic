import { DataPoint } from '@interfaces';
import axios from 'axios';

export const ENDPOINT = '/data_points';

export async function updateDataPoint (dataPoint: DataPoint): Promise<DataPoint> {
    return await axios.put(`${ENDPOINT}/${dataPoint.id}`, dataPoint)
        .then(resp => resp.data.data);
}
