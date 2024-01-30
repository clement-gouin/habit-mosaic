<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\User;
use App\Services\Mosaic\DayMosaicService;
use App\Services\TableService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TableDataControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_current_month_data(): void
    {
        $user = User::factory()->create();

        $this->getMock(TableService::class)
            ->expects('getTableData')
            ->with(self::modelArg($user), self::dateArg(Carbon::today()), 31);

        $this->mockMosaicServiceStatistics(DayMosaicService::class, $user);

        $this->actingAs($user)
            ->getJson(route('table.data'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_shows_specific_span_data(): void
    {
        $user = User::factory()->create();

        $date = new Carbon(fake()->date);
        $span = fake()->randomNumber(nbDigits: 2, strict: true) + 2;

        $this->getMock(TableService::class)
            ->expects('getTableData')
            ->with(self::modelArg($user), self::dateArg($date), $span);

        $this->mockMosaicServiceStatistics(DayMosaicService::class, $user);

        $this->actingAs($user)
            ->getJson(route('table.data', ['date' => $date->format('Y-m-d'), 'days' => $span]))
            ->assertSuccessful();
    }
}
