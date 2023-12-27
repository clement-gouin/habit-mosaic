<?php

namespace Tests\Feature\Controllers\Web;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tracker;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DashboardControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_view(): void
    {
        $this->actingAs(User::factory()->create())
            ->getJson(route('dashboard'))
            ->assertSuccessful()
            ->assertViewIs('dashboard');
    }
}
