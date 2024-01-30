<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Services\Mosaic\CategoryMosaicService;
use Illuminate\Http\Request;

/** @property Category $resource */
class CategoryFullResource extends CategoryResource
{
    /**
     * Transform the resource into an array.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'statistics' => StatisticsResource::make(CategoryMosaicService::instance()->getStatistics($this->resource)),
        ]);
    }
}
