<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\StatisticsResource;
use App\Http\Resources\TrackerFullResource;
use App\Models\DataPoint;
use App\Models\Tracker;
use App\Services\Mosaic\TrackerMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;
use Tests\TestCase;

class TrackerFullResourceTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_makes_statistics(): void
    {
        $tracker = Tracker::factory()->create();

        $this->mockMosaicServiceStatistics(TrackerMosaicService::class, $tracker);

        $resource = TrackerFullResource::make($tracker);

        $data = $resource->toArray(Request::create(''));

        $this->assertEquals($tracker->id, $data['id']);
        $this->assertInstanceOf(StatisticsResource::class, $data['statistics']);
    }

    /** @test */
    public function it_gets_specific_day_data_point(): void
    {
        $date = (new Carbon(fake()->dateTimeBetween('-10 days')))->startOfDay();

        $tracker = Tracker::factory()->create();

        $this->mockMosaicServiceStatistics(TrackerMosaicService::class, $tracker);

        $dataPoint = DataPoint::factory()->create([
            'tracker_id' => $tracker->id,
            'date' => $date,
        ]);

        $resource = TrackerFullResource::make($tracker->refresh());

        $request = Request::create('', parameters: ['date' => $date->format('Y-m-d')]);

        $data = $resource->toArray($request);

        $this->assertEquals($tracker->id, $data['id']);
        $this->assertEquals($dataPoint->id, $data['data_point']['id']);
        $this->assertEquals($dataPoint->value, $data['data_point']['value']);
    }

    public function it_creates_missing_data_point(): void
    {
        $date = (new Carbon(fake()->dateTimeBetween('-10 days')))->startOfDay();

        $tracker = Tracker::factory()->create();

        $this->mockMosaicServiceStatistics(TrackerMosaicService::class, $tracker);

        $resource = TrackerFullResource::make($tracker->refresh());

        $request = Request::create('', parameters: ['date' => $date->format('Y-m-d')]);

        $data = $resource->toArray($request);

        $this->assertEquals($tracker->id, $data['id']);
        $this->assertEquals(0, $data['data_point']['value']);

        $this->assertDatabaseHas('data_points', [
            'tracker_id' => $tracker->id,
            'date' => $date,
            'value' => 0,
        ]);
    }

    /** @test */
    public function it_falls_back_to_todays_data_point_when_missing_date(): void
    {
        $tracker = Tracker::factory()->create();

        $this->mockMosaicServiceStatistics(TrackerMosaicService::class, $tracker);

        $dataPoint = DataPoint::factory()->create([
            'tracker_id' => $tracker->id,
            'date' => Carbon::today(),
        ]);

        $resource = TrackerFullResource::make($tracker->refresh());

        $request = Request::create('');

        $data = $resource->toArray($request);

        $this->assertEquals($tracker->id, $data['id']);
        $this->assertEquals($dataPoint->id, $data['data_point']['id']);
        $this->assertEquals($dataPoint->value, $data['data_point']['value']);
    }

    /** @test */
    public function it_falls_back_to_todays_data_point_when_invalid_date(): void
    {
        $tracker = Tracker::factory()->create();

        $this->mockMosaicServiceStatistics(TrackerMosaicService::class, $tracker);

        $dataPoint = DataPoint::factory()->create([
            'tracker_id' => $tracker->id,
            'date' => Carbon::today(),
        ]);

        $resource = TrackerFullResource::make($tracker->refresh());

        $request = Request::create('', parameters: ['date' => 'invalid']);

        $data = $resource->toArray($request);

        $this->assertEquals($tracker->id, $data['id']);
        $this->assertEquals($dataPoint->id, $data['data_point']['id']);
        $this->assertEquals($dataPoint->value, $data['data_point']['value']);
    }
}
