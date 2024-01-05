<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Services\DataPointService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DataPointServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected DataPointService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[DataPointService::class];
    }

    /** @test */
    public function it_doesnt_updates_value_not_changed(): void
    {
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_forces_minimum_value(): void
    {
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_forces_maximum_value(): void
    {
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_doesnt_forces_maximum_value_on_overflow(): void
    {
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_clears_mosaic_data(): void
    {
        $this->markTestSkipped('TODO');
    }
}
