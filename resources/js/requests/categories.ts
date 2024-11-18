import { Category, CategoryData } from '@interfaces';
import axios, { AxiosError } from 'axios';
import { useAlertsStore } from '@stores/alerts';

export const ENDPOINT = '/api/categories';

const { alertSuccess } = useAlertsStore();

let controller: AbortController;

export async function listCategories (): Promise<Category[]> {
    const url = `${ENDPOINT}/list`;

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

export async function createCategory (category: CategoryData): Promise<Category> {
    return await axios.post(ENDPOINT, category)
        .then(resp => {
            alertSuccess('Category created');
            return resp.data.data;
        });
}

export async function updateCategory (category: CategoryData): Promise<Category> {
    return await axios.put(`${ENDPOINT}/${category.id as number}`, category)
        .then(resp => {
            alertSuccess('Category updated');
            return resp.data.data;
        });
}

export async function deleteCategory (category: CategoryData): Promise<void> {
    await axios.delete(`${ENDPOINT}/${category.id as number}`)
        .then(() => {
            alertSuccess('Category deleted');
        });
}
