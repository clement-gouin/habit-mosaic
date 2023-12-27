<?php

namespace Tests\Feature\Controllers\Web;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tracker;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TableViewControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_view(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create([
            'user_id' => $user->id,
        ]);

        $tracker = Tracker::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->actingAs($user)
            ->getJson(route('table'))
            ->assertSuccessful()
            ->assertViewIs('table_view')
            ->assertViewHas('categories')
            ->assertViewHas('trackers')
            ->assertViewHas('data');
    }
}
