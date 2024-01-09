<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tracker;
use App\Models\Category;
use App\Models\DataPoint;
use Illuminate\Support\Carbon;
use App\Events\TrackerUpdated;
use App\Events\CategoryUpdated;
use App\Services\TrackerService;
use Illuminate\Support\Facades\Event;
use App\Console\Commands\CleanEmptyDays;
use App\Console\Commands\ComputeAverageDay;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CleanEmptyDaysTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_cleans_empty_day(): void
    {
        $user = User::factory()->create();

        $tracker = Tracker::factory()->create([
            'user_id' => $user->id,
            'single' => false,
            'target_value' => 2,
            'target_score' => 1.5,
        ]);

        $tracker->dataPoints()->saveMany([
            DataPoint::factory()->make([
                'date' => Carbon::today(),
                'value' => 0,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::yesterday(),
                'value' => 0,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::createFromTimestamp(0),
                'value' => 0,
            ]),
        ]);

        $this->artisan(CleanEmptyDays::class);

        $this->assertDatabaseHas('data_points', [
            'tracker_id' => $tracker->id,
            'date' => Carbon::today(),
        ]);

        $this->assertDatabaseMissing('data_points', [
            'tracker_id' => $tracker->id,
            'date' => Carbon::yesterday(),
        ]);

        $this->assertDatabaseHas('data_points', [
            'tracker_id' => $tracker->id,
            'date' => Carbon::createFromTimestamp(0),
        ]);
    }
}
