<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tracker;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrackerControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_creates_tracker(): void
    {
        $user = User::factory()->create();

        $targetData = $this->getTargetData();

        $this->actingAs($user)
            ->post(route('trackers.store'), $targetData)
            ->assertSuccessful();

        $this->assertDatabaseHas('trackers', [
            'user_id' => $user->id,
            ...$targetData,
        ]);
    }

    /** @test */
    public function it_forbid_data_point_update_for_another_user(): void
    {
        $tracker = Tracker::factory()->create();

        $targetData = $this->getTargetData();

        $this->actingAs(User::factory()->create())
            ->put(route('trackers.update', $tracker), $targetData)
            ->assertStatus(403);

        $this->assertDatabaseMissing('trackers', [
            'id' => $tracker->id,
            ...$targetData,
        ]);
    }

    /** @test */
    public function it_updates_tracker(): void
    {
        $tracker = Tracker::factory()->create();

        $targetData = $this->getTargetData();

        $this->actingAs($tracker->user)
            ->put(route('trackers.update', $tracker), $targetData)
            ->assertSuccessful();

        $this->assertDatabaseHas('trackers', [
            'id' => $tracker->id,
            ...$targetData,
        ]);
    }

    /** @test */
    public function it_forbid_data_point_deletion_for_another_user(): void
    {
        $tracker = Tracker::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('trackers.destroy', $tracker))
            ->assertStatus(403);

        $this->assertDatabaseHas('trackers', [
            'id' => $tracker->id,
        ]);
    }

    /** @test */
    public function it_deletes_tracker(): void
    {
        $tracker = Tracker::factory()->create();

        $this->actingAs($tracker->user)
            ->delete(route('trackers.destroy', $tracker))
            ->assertSuccessful();

        $this->assertDatabaseMissing('trackers', [
            'id' => $tracker->id,
        ]);
    }

    protected function getTargetData(): array
    {
        return [
            'name' => fake()->word(),
            'icon' => fake()->word(),
            'order' => fake()->randomNumber(nbDigits: 3),
            'unit' => fake()->boolean() ? fake()->word() : null,
            'value_step' => fake()->randomFloat(),
            'default_value' => fake()->randomFloat(),
            'target_value' => fake()->randomFloat(),
            'target_score' => fake()->randomFloat(),
            'target_max' => fake()->boolean,
        ];
    }
}
