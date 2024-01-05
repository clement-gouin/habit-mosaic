<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Services\TableService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TableServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected TableService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[TableService::class];
    }

    /** @test */
    public function it_gets_current_month_data(): void
    {
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_gets_another_day_data(): void
    {
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_gets_another_span_data(): void
    {
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_gets_current_month_data_on_parse_fail(): void
    {
        $this->markTestSkipped('TODO');
    }
}
