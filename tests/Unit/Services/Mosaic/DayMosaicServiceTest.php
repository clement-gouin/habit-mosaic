<?php

namespace Tests\Unit\Services\Mosaic;

use App\Models\Tracker;
use App\Models\User;
use App\Services\Mosaic\DayMosaicService;
use App\Services\Mosaic\MosaicService;
use App\Services\Mosaic\TrackerMosaicService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DayMosaicServiceTest extends MosaicServiceTestCase
{
    use DatabaseMigrations;

    public function makeService(): MosaicService
    {
        return new DayMosaicService(new TrackerMosaicService());
    }

    public function createTarget(): Model
    {
        return User::factory()->create();
    }

    public function getCacheRootKey(Model|User $target): string
    {
        return 'mosaic.day.'.$target->id;
    }

    public function attachDataPointsToTarget(Model|User $target, array $dataPoints): void
    {
        $tracker = Tracker::factory()->create([
            'user_id' => $target->id,
            'value_step' => 1,
            'target_value' => 1,
            'single' => true,
            'target_score' => 1,
            'overflow' => true,
        ]);

        $tracker->dataPoints()->saveMany($dataPoints);
    }
}
