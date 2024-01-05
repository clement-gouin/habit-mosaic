<?php

namespace Tests\Feature\Services\Mosaic;

use Tests\TestCase;
use App\Services\Mosaic\DayMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DayMosaicServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected DayMosaicService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[DayMosaicService::class];
    }

    /** @test */
    public function it_gets_mosaic_data_empty(): void
    {
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_gets_mosaic_data_with_data(): void
    {
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_can_clear_data_for_week(): void
    {
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_wipe_data(): void
    {
        $this->markTestSkipped('TODO');
    }
}
