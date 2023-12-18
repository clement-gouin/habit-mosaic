import { DataPoint } from '@interfaces';
import axios from 'axios';
import { useAlertsStore } from '@stores/alerts';

export const ENDPOINT = '/api/data_points';

const { alertAxiosError } = useAlertsStore();

export async function updateDataPoint (dataPoint: DataPoint): Promise<DataPoint> {
    return await axios.put(`${ENDPOINT}/${dataPoint.id}`, dataPoint)
        .then(resp => resp.data.data)
        .catch(alertAxiosError);
}
