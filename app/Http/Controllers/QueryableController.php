<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class QueryableController extends Controller
{
    protected const ORDER_COLUMNS = [];

    protected const FILTER_COLUMNS = [];

    abstract public function getData(Request $request): LengthAwarePaginator;

    protected function addOrder(Builder $query, Request $request, string $defaultSort = 'id'): Builder
    {
        $param = $request->get('sortBy', $defaultSort);

        $orderDirection = Str::startsWith($param, '-') ? 'desc' : 'asc';
        $orderBy = static::ORDER_COLUMNS[Str::replaceFirst('-', '', $param)] ?? 'id';

        return $query->orderBy(
            $orderBy,
            $orderDirection,
        );
    }

    protected function addFilters(Builder $query, Request $request): Builder
    {
        $filters = $request->get('filters', []);

        foreach ($filters as $key => $value) {
            if (array_key_exists($key, static::FILTER_COLUMNS) && $value) {
                $query->where(static::FILTER_COLUMNS[$key], Arr::wrap($value));
            }
        }

        return $query;
    }

    protected function addSearch(Builder $query, Request $request, array $columns): Builder
    {
        if ($request->has('search')) {
            $query->where(function ($query) use ($columns, $request) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'ILIKE', '%' . $request->get('search') . '%');
                }
            });
        }

        return $query;
    }

    protected function paginate(Builder $query, Request $request): LengthAwarePaginator
    {
        return $query->paginate(perPage: $request->integer('perPage', 10));
    }
}
