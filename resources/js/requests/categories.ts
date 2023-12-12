import { Category, QueryParameters } from '@interfaces';
import axios, { AxiosError } from 'axios';
import { toURLParameters } from '@utils/url';

export const ENDPOINT = '/categories';

let controller: AbortController;

export async function listCategories (params: QueryParameters): Promise<Category[]> {
    const url = `${ENDPOINT}/list?${toURLParameters(params)}`;

    controller?.abort();

    controller = new AbortController();

    try {
        return await axios.get(url)
            .then(resp => resp.data.data as Category[]);
    } catch (error) {
        if ((error as AxiosError).message === 'canceled') {
            return [];
        }
        throw error;
    }
}

export async function createCategory (category: Category): Promise<Category> {
    return await axios.post(ENDPOINT, category)
        .then(resp => resp.data.data);
}

export async function updateCategory (category: Category): Promise<Category> {
    return await axios.put(`${ENDPOINT}/${category.id as number}`, category)
        .then(resp => resp.data.data);
}

export async function deleteCategory (category: Category): Promise<never> {
    return await axios.delete(`${ENDPOINT}/${category.id as number}`);
}
