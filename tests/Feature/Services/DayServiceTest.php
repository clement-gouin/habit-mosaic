<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Services\DayService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DayServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected DayService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[DayService::class];
    }

    /** @test */
    public function it_gets_today_data(): void
    {
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_gets_another_day_data(): void
    {
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_gets_today_data_on_parse_fail(): void
    {
        $this->markTestSkipped('TODO');
    }
}
