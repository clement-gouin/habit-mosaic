<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tracker;
use App\Models\Category;
use App\Models\DataPoint;
use App\Services\TableService;
use Illuminate\Support\Carbon;
use App\Http\Resources\TrackerResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\DataPointResource;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Resources\Json\ResourceCollection;

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
    public function it_gets_current_span_data(): void
    {
        $user = User::factory()->create();

        $span = fake()->randomNumber(nbDigits: 2, strict: true) + 2;

        $category = Category::factory()->create([
            'user_id' => $user->id,
        ]);

        $tracker = Tracker::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'single' => false,
            'target_value' => 2,
            'target_score' => 1.5,
        ]);

        $tracker->dataPoints()->save(
            DataPoint::factory()->make([
                'date' => Carbon::createFromTimestamp(0),
                'value' => 2,
            ])
        );

        $result = $this->service->getTableData($user, Carbon::today(), $span);

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
