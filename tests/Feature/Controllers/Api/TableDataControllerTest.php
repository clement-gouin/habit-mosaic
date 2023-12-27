<?php

namespace Tests\Feature\Controllers\Api;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tracker;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TableDataControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_month_data(): void
    {
        $tracker = Tracker::factory()->create();

        $this->actingAs($tracker->user)
            ->getJson(route('table.data'))
            ->assertSuccessful()
            ->assertJsonCount(31, 'data');

        $this->assertDatabaseCount('data_points', 31);
    }
}
