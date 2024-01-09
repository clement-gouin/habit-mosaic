<?php

namespace Tests\Feature\Controllers\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tracker;
use App\Models\Category;
use App\Models\DataPoint;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TableDataControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_current_month_data(): void
    {
        $tracker = Tracker::factory()->create();

        $result = $this->actingAs($tracker->user)
            ->getJson(route('table.data'))
            ->assertSuccessful()
            ->assertJsonCount(31, 'data')
            ->json('data');

        $minDate = Carbon::today()->format('Y-m-d');
        $this->assertArrayHasKey($minDate, $result);

        $maxDate = Carbon::today()->subDays(30)->format('Y-m-d');
        $this->assertArrayHasKey($maxDate, $result);
    }

    /** @test */
    public function it_shows_specific_span_data(): void
    {
        $user = User::factory()->create();

        $date = new Carbon(fake()->date);
        $span = fake()->randomNumber(nbDigits: 2, strict: true) + 2;

        $result = $this->actingAs($user)
            ->getJson(route('table.data', ['date' => $date->format('Y-m-d'), 'days' => $span]))
            ->assertSuccessful()
            ->assertJsonCount($span, 'data')
            ->json('data');

        $minDate = $date->format('Y-m-d');
        $this->assertArrayHasKey($minDate, $result);

        $maxDate = $date->subDays($span - 1)->format('Y-m-d');
        $this->assertArrayHasKey($maxDate, $result);
    }
}
