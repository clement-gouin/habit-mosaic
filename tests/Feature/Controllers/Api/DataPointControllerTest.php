<?php

namespace Tests\Feature\Controllers\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tracker;
use App\Models\DataPoint;
use App\Events\DataPointUpdated;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DataPointControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_forbid_data_point_update_for_another_user(): void
    {
        $dataPoint = DataPoint::factory()->create([
            'tracker_id' => Tracker::factory()->create([
                'overflow' => true,
                'value_step' => 1,
            ]),
        ]);

        $targetData = [
            'value' => fake()->randomNumber(),
        ];

        $this->actingAs(User::factory()->create())
            ->putJson(route('data_points.update', $dataPoint), $targetData)
            ->assertStatus(403);

        $this->assertDatabaseMissing('data_points', [
            'id' => $dataPoint->id,
            ...$targetData,
        ]);

        Event::assertNotDispatched(DataPointUpdated::class);
    }

    /** @test */
    public function it_updates_data_point(): void
    {
        $dataPoint = DataPoint::factory()->create([
            'tracker_id' => Tracker::factory()->create([
                'overflow' => true,
                'value_step' => 1,
            ]),
        ]);

        $targetData = [
            'value' => fake()->randomNumber(),
        ];

        $this->actingAs($dataPoint->tracker->user)
            ->putJson(route('data_points.update', $dataPoint), $targetData)
            ->assertSuccessful();

        $this->assertDatabaseHas('data_points', [
            'id' => $dataPoint->id,
            ...$targetData,
        ]);

        Event::assertDispatched(
            DataPointUpdated::class,
            fn (DataPointUpdated $event) => $event->dataPoint->id === $dataPoint->id
        );
    }
}
