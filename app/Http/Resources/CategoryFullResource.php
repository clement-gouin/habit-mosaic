<?php

namespace App\Http\Resources;

use App\Models\Tracker;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property Category $resource */
class CategoryFullResource extends CategoryResource
{
    /**
     * Transform the resource into an array.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'average' => $this->resource->trackers->sum(fn (Tracker $tracker) => $tracker->getAverageScore()),
        ]);
    }
}
