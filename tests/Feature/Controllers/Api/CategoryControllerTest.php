<?php

namespace Tests\Feature\Controllers\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_lists_categories(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($category->user)
            ->getJson(route('categories.list'))
            ->assertSuccessful()
            ->assertJsonFragment(['id' => $category->id]);
    }

    /** @test */
    public function it_creates_category(): void
    {
        $user = User::factory()->create();

        $targetData = $this->getTargetData();

        $this->actingAs($user)
            ->postJson(route('categories.store'), $targetData)
            ->assertSuccessful();

        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            ...$targetData,
        ]);
    }

    /** @test */
    public function it_forbid_category_update_for_another_user(): void
    {
        $category = Category::factory()->create();

        $targetData = $this->getTargetData();

        $this->actingAs(User::factory()->create())
            ->putJson(route('categories.update', $category), $targetData)
            ->assertForbidden();

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
            ...$targetData,
        ]);
    }

    /** @test */
    public function it_updates_category(): void
    {
        $category = Category::factory()->create();

        $targetData = $this->getTargetData();

        $this->actingAs($category->user)
            ->putJson(route('categories.update', $category), $targetData)
            ->assertSuccessful();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            ...$targetData,
        ]);
    }

    /** @test */
    public function it_forbid_category_deletion_for_another_user(): void
    {
        $category = Category::factory()->create();

        $this->actingAs(User::factory()->create())
            ->deleteJson(route('categories.destroy', $category))
            ->assertForbidden();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }

    /** @test */
    public function it_deletes_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($category->user)
            ->deleteJson(route('categories.destroy', $category))
            ->assertSuccessful();

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    protected function getTargetData(): array
    {
        return [
            'name' => fake()->word(),
            'icon' => fake()->word(),
            'order' => fake()->randomNumber(nbDigits: 3),
        ];
    }
}
