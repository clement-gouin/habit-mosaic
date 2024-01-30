<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\DataPoint;
use App\Models\Tracker;
use App\Models\User;
use App\Services\DataPointService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

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

        $this->getMock(DataPointService::class)
            ->expects('updateValue')
            ->with(self::modelArg($dataPoint), $targetData['value']);

        $this->actingAs($dataPoint->tracker->user)
            ->putJson(route('data_points.update', $dataPoint), $targetData)
            ->assertSuccessful();
    }
}
