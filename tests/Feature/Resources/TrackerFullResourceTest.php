<?php

namespace Tests\Feature\Resources;

use Tests\TestCase;
use App\Models\Tracker;
use App\Models\DataPoint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;
use App\Http\Resources\DataPointResource;
use App\Http\Resources\TrackerFullResource;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrackerFullResourceTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_makes_empty_average(): void
    {
        $tracker = Tracker::factory()->create();

        $resource = TrackerFullResource::make($tracker);

        $data = $resource->toArray(Request::create(''));

        $this->assertEquals($tracker->id, $data['id']);
        $this->assertEquals(0, $data['average']);

        $this->assertDatabaseHas('data_points', [
            'tracker_id' => $tracker->id,
            'date' => Carbon::createFromTimestamp(0),
            'value' => 0,
        ]);
    }

    /** @test */
    public function it_returns_average(): void
    {
        $tracker = Tracker::factory()->create();

        $tracker->dataPoints()->save(
            DataPoint::factory()->make([
                'date' => Carbon::createFromTimestamp(0),
                'value' => 1.5,
            ])
        );

        $resource = TrackerFullResource::make($tracker);

        $data = $resource->toArray(Request::create(''));

        $this->assertEquals($tracker->id, $data['id']);
        $this->assertEquals(1.5, $data['average']);
    }

    /** @test */
    public function it_gets_specific_day_data_point(): void
    {
        $date = (new Carbon(fake()->dateTimeBetween('-10 days')))->startOfDay();

        $tracker = Tracker::factory()->create();

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
