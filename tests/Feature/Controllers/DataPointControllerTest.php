<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\DataPoint;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DataPointControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_forbid_data_point_update_for_another_user(): void
    {
        $dataPoint = DataPoint::factory()->create();

        $targetData = $this->getTargetData();

        $this->actingAs(User::factory()->create())
            ->put(route('data_points.update', $dataPoint), $targetData)
            ->assertStatus(403);

        $this->assertDatabaseMissing('data_points', [
            'id' => $dataPoint->id,
            ...$targetData,
        ]);
    }

    /** @test */
    public function it_updates_data_point(): void
    {
        $dataPoint = DataPoint::factory()->create();

        $targetData = $this->getTargetData();

        $this->actingAs($dataPoint->tracker->user)
            ->put(route('data_points.update', $dataPoint), $targetData)
            ->assertSuccessful();

        $this->assertDatabaseHas('data_points', [
            'id' => $dataPoint->id,
            ...$targetData,
        ]);
    }

    protected function getTargetData(): array
    {
        return [
            'value' => fake()->randomFloat(-30, 30),
        ];
    }
}
