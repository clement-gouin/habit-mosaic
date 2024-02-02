<?php

namespace Tests\Unit\Services;

use App\Http\Resources\DataPointResource;
use App\Models\Tracker;
use App\Models\User;
use App\Services\TableService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TableServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected TableService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new TableService();
    }

    /** @test */
    public function it_gets_current_span_data(): void
    {
        $span = fake()->randomNumber(nbDigits: 2, strict: true) + 2;

        $tracker = Tracker::factory()->create([
            'single' => false,
            'target_value' => 2,
            'target_score' => 1.5,
        ]);

        $result = $this->service->getTableData($tracker->user, Carbon::today(), $span);

        $today = Carbon::today()->format('Y-m-d');
        $this->assertArrayHasKey($today, $result);
        $this->assertCount(1, $result[$today]);
        $this->assertInstanceOf(DataPointResource::class, $result[$today][0]);

        $maxDate = Carbon::today()->subDays($span - 1)->format('Y-m-d');
        $this->assertArrayHasKey($maxDate, $result);
        $this->assertCount(1, $result[$maxDate]);
        $this->assertInstanceOf(DataPointResource::class, $result[$maxDate][0]);
    }

    /** @test */
    public function it_gets_another_day_data(): void
    {
        $user = User::factory()->create();

        $result = $this->service->getTableData($user, Carbon::yesterday(), 7);

        $this->assertArrayNotHasKey(Carbon::today()->format('Y-m-d'), $result);
        $this->assertArrayHasKey(Carbon::yesterday()->format('Y-m-d'), $result);
    }
}
