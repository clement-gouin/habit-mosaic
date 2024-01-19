<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Tracker;
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
            'average' => $this->resource->trackers->sum(fn (Tracker $tracker) => $tracker->getAverageScore()),
        ]);
    }
}
