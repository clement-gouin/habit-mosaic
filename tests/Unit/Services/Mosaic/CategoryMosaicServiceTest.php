<?php

namespace Tests\Unit\Services\Mosaic;

use App\Models\Category;
use App\Models\Tracker;
use App\Services\Mosaic\CategoryMosaicService;
use App\Services\Mosaic\MosaicService;
use App\Services\Mosaic\TrackerMosaicService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CategoryMosaicServiceTest extends MosaicServiceTestCase
{
    use DatabaseMigrations;

    public function makeService(): MosaicService
    {
        return new CategoryMosaicService(new TrackerMosaicService());
    }

    public function createTarget(): Model
    {
        $category = Category::factory()->create();

        Tracker::factory()->create([
            'category_id' => $category->id,
            'value_step' => 1,
            'target_value' => 1,
            'single' => true,
            'target_score' => 1,
            'overflow' => true,
        ]);

        return $category;
    }

    public function getCacheRootKey(Model|Category $target): string
    {
        return 'mosaic.category.'.$target->id;
    }

    public function attachDataPointsToTarget(Model|Category $target, array $dataPoints): void
    {
        /** @var Tracker $tracker */
        $tracker = $target->trackers()->first();

        $tracker->dataPoints()->saveMany($dataPoints);
    }
}
