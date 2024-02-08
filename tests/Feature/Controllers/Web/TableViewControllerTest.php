<?php

namespace Tests\Feature\Controllers\Web;

use App\Models\User;
use App\Services\Mosaic\DayMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TableViewControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_view(): void
    {
        $user = User::factory()->create();

        $this->mockMosaicServiceStatistics(DayMosaicService::class, $user);

        $this->actingAs($user)
            ->getJson(route('table'))
            ->assertSuccessful()
            ->assertViewIs('table_view')
            ->assertViewHas('date', Carbon::today()->format('Y-m-d'))
            ->assertViewHas('days', 31)
            ->assertViewHas('statistics')
            ->assertViewHas('categories')
            ->assertViewHas('trackers');
    }
}
