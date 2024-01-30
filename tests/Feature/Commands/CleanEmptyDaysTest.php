<?php

namespace Tests\Feature\Commands;

use App\Console\Commands\CleanEmptyDays;
use App\Models\User;
use App\Services\DayService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CleanEmptyDaysTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_cleans_empty_day(): void
    {
        $user = User::factory()->create();

        $this->getMock(DayService::class)
            ->expects('cleanEmptyDays')
            ->with(self::modelArg($user))
            ->andReturn(0);

        $this->artisan(CleanEmptyDays::class);
    }
}
