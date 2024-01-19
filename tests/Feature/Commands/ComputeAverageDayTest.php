<?php

namespace Tests\Feature\Commands;

use App\Console\Commands\ComputeAverageDay;
use App\Models\DataPoint;
use App\Models\Tracker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ComputeAverageDayTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_updates_average_for_tracker_with_data(): void
    {
        $tracker = Tracker::factory()->create();

        $tracker->dataPoints()->save(
            DataPoint::factory()->make([
                'date' => fake()->dateTimeBetween('-10 days'),
                'value' => 1.5,
            ])
        );

        $this->artisan(ComputeAverageDay::class);

        $this->assertDatabaseHas('data_points', [
            'tracker_id' => $tracker->id,
            'date' => Carbon::createFromTimestamp(0),
            'value' => 1.5,
        ]);
    }
}
