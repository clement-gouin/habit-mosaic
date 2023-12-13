import { Category, CategoryData } from '@interfaces';
import axios, { AxiosError } from 'axios';

export const ENDPOINT = '/categories';

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
        .then(resp => resp.data.data);
}

export async function updateCategory (category: CategoryData): Promise<Category> {
    return await axios.put(`${ENDPOINT}/${category.id as number}`, category)
        .then(resp => resp.data.data);
}

export async function deleteCategory (category: CategoryData): Promise<never> {
    return await axios.delete(`${ENDPOINT}/${category.id as number}`);
}
