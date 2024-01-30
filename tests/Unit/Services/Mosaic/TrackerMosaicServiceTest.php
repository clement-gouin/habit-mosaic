<?php

namespace Tests\Unit\Services\Mosaic;

use App\Models\Tracker;
use App\Services\Mosaic\MosaicService;
use App\Services\Mosaic\TrackerMosaicService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrackerMosaicServiceTest extends MosaicServiceTestCase
{
    use DatabaseMigrations;

    public function makeService(): MosaicService
    {
        return new TrackerMosaicService();
    }

    public function createTarget(): Model
    {
        return Tracker::factory()->create([
            'value_step' => 1,
            'target_value' => 1,
            'single' => true,
            'target_score' => 1,
            'overflow' => true,
        ]);
    }

    public function getCacheRootKey(Model|Tracker $target): string
    {
        return 'mosaic.tracker.'.$target->id;
    }

    public function attachDataPointsToTarget(Model|Tracker $target, array $dataPoints): void
    {
        $target->dataPoints()->saveMany($dataPoints);
    }
}
