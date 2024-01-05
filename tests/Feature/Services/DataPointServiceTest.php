<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Models\Tracker;
use App\Models\DataPoint;
use App\Events\DataPointUpdated;
use App\Services\DataPointService;
use Illuminate\Support\Facades\Event;
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
        $dataPoint = DataPoint::factory()->create([
            'tracker_id' => Tracker::factory()->create([
                'overflow' => true,
                'value_step' => 1,
            ]),
            'value' => fake()->randomNumber(),
        ]);

        $this->service->updateValue($dataPoint, $dataPoint->value);

        Event::assertNotDispatched(DataPointUpdated::class);

        $this->assertDatabaseHas('data_points', [
            'id' => $dataPoint->id,
            'value' => $dataPoint->value,
        ]);
    }

    /** @test */
    public function it_forces_minimum_value(): void
    {
        $dataPoint = DataPoint::factory()->create([
            'tracker_id' => Tracker::factory()->create([
                'overflow' => true,
                'value_step' => 1,
            ]),
            'value' => fake()->randomNumber(nbDigits: 2),
        ]);

        $this->service->updateValue($dataPoint, -(1 + fake()->randomNumber()));

        Event::assertDispatched(
            DataPointUpdated::class,
            fn (DataPointUpdated $event) => $event->dataPoint->id === $dataPoint->id,
        );

        $this->assertDatabaseHas('data_points', [
            'id' => $dataPoint->id,
            'value' => 0,
        ]);
    }

    /** @test */
    public function it_forces_maximum_value(): void
    {
        $max = fake()->randomNumber() + 1;

        $dataPoint = DataPoint::factory()->create([
            'tracker_id' => Tracker::factory()->create([
                'overflow' => false,
                'target_value' => $max,
                'value_step' => 1,
            ]),
            'value' => fake()->randomNumber(nbDigits: 2, strict: true),
        ]);

        $this->service->updateValue($dataPoint, $max + 1);

        Event::assertDispatched(
            DataPointUpdated::class,
            fn (DataPointUpdated $event) => $event->dataPoint->id === $dataPoint->id,
        );

        $this->assertDatabaseHas('data_points', [
            'id' => $dataPoint->id,
            'value' => $max,
        ]);
    }

    /** @test */
    public function it_doesnt_forces_maximum_value_on_overflow(): void
    {
        $max = fake()->randomNumber() + 1;

        $dataPoint = DataPoint::factory()->create([
            'tracker_id' => Tracker::factory()->create([
                'overflow' => true,
                'target_value' => $max,
                'value_step' => 1,
            ]),
            'value' => fake()->randomNumber(nbDigits: 2, strict: true),
        ]);

        $this->service->updateValue($dataPoint, $max + 1);

        Event::assertDispatched(
            DataPointUpdated::class,
            fn (DataPointUpdated $event) => $event->dataPoint->id === $dataPoint->id,
        );

        $this->assertDatabaseHas('data_points', [
            'id' => $dataPoint->id,
            'value' => $max + 1,
        ]);
    }

    /** @test */
    public function it_doesnt_forces_value_step(): void
    {
        $rootValue = fake()->randomNumber();

        $dataPoint = DataPoint::factory()->create([
            'tracker_id' => Tracker::factory()->create([
                'overflow' => true,
                'value_step' => 0.5,
            ]),
            'value' => fake()->randomNumber(nbDigits: 2, strict: true),
        ]);

        $this->service->updateValue($dataPoint, $rootValue + 0.34);

        Event::assertDispatched(
            DataPointUpdated::class,
            fn (DataPointUpdated $event) => $event->dataPoint->id === $dataPoint->id,
        );

        $this->assertDatabaseHas('data_points', [
            'id' => $dataPoint->id,
            'value' => $rootValue + 0.5,
        ]);
    }
}
