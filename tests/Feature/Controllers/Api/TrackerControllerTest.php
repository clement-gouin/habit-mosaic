<?php

namespace Tests\Feature\Controllers\Api;

use App\Events\TrackerDeleted;
use App\Models\Category;
use App\Models\Tracker;
use App\Models\User;
use App\Services\TrackerService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TrackerControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_lists_tracker(): void
    {
        $tracker = Tracker::factory()->create();

        $this->actingAs($tracker->user)
            ->getJson(route('trackers.list'))
            ->assertSuccessful()
            ->assertJsonFragment(['id' => $tracker->id]);
    }

    /** @test */
    public function it_creates_tracker_without_category(): void
    {
        $category = Category::factory()->create();

        $targetData = $this->getTargetData($category);

        $this->actingAs($category->user)
            ->postJson(route('trackers.store'), $targetData)
            ->assertSuccessful();

        $this->assertDatabaseHas('trackers', [
            ...$targetData,
        ]);
    }

    /** @test */
    public function it_cannot_create_tracker_with_other_user_category(): void
    {
        $category = Category::factory()->create();

        $targetData = $this->getTargetData($category);

        $this->actingAs(User::factory()->create())
            ->postJson(route('trackers.store'), $targetData)
            ->assertUnprocessable();

        $this->assertDatabaseMissing('trackers', [
            ...$targetData,
        ]);
    }

    /** @test */
    public function it_forbid_tracker_update_for_another_user(): void
    {
        $tracker = Tracker::factory()->create();

        $targetData = $this->getTargetData($tracker->category);

        $this->actingAs(User::factory()->create())
            ->putJson(route('trackers.update', $tracker), $targetData)
            ->assertForbidden();

        $this->assertDatabaseMissing('trackers', [
            'id' => $tracker->id,
            ...$targetData,
        ]);
    }

    /** @test */
    public function it_updates_tracker(): void
    {
        $tracker = Tracker::factory()->create();

        $targetData = $this->getTargetData($tracker->category);

        $this->getMock(TrackerService::class)
            ->expects('update')
            ->with(self::modelArg($tracker), $targetData);

        $this->actingAs($tracker->user)
            ->putJson(route('trackers.update', $tracker), $targetData)
            ->assertSuccessful();
    }

    /** @test */
    public function it_cannot_update_tracker_with_other_user_category(): void
    {
        $category = Category::factory()->create();

        $tracker = Tracker::factory()->create();

        $targetData = $this->getTargetData($category);

        $this->actingAs($tracker->user)
            ->putJson(route('trackers.update', $tracker), $targetData)
            ->assertUnprocessable();
    }

    /** @test */
    public function it_forbid_tracker_deletion_for_another_user(): void
    {
        $tracker = Tracker::factory()->create();

        $this->actingAs(User::factory()->create())
            ->deleteJson(route('trackers.destroy', $tracker))
            ->assertForbidden();

        $this->assertDatabaseHas('trackers', [
            'id' => $tracker->id,
        ]);
    }

    /** @test */
    public function it_deletes_tracker(): void
    {
        $tracker = Tracker::factory()->create();

        $this->actingAs($tracker->user)
            ->deleteJson(route('trackers.destroy', $tracker))
            ->assertSuccessful();

        Event::assertDispatched(
            TrackerDeleted::class,
            fn (TrackerDeleted $event) => $event->tracker->id === $tracker->id
        );

        $this->assertDatabaseMissing('trackers', [
            'id' => $tracker->id,
        ]);
    }

    protected function getTargetData(Category $category): array
    {
        return [
            'category_id' => $category->id,
            'name' => fake()->word(),
            'icon' => fake()->word(),
            'order' => fake()->randomNumber(nbDigits: 3),
            'unit' => fake()->boolean() ? fake()->word() : null,
            'value_step' => fake()->randomFloat(min: 0.1),
            'target_value' => fake()->randomFloat(min: 1),
            'target_score' => fake()->randomFloat(min: 0.1),
            'single' => fake()->boolean,
            'overflow' => fake()->boolean,
        ];
    }
}
