<?php

namespace Tests\Feature\Resources;

use Tests\TestCase;
use App\Models\Tracker;
use App\Models\Category;
use App\Models\DataPoint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;
use App\Http\Resources\CategoryFullResource;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CategoryFullResourceTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_average_without_data(): void
    {
        $category = Category::factory()->create();

        $resource = CategoryFullResource::make($category);

        $data = $resource->toArray(Request::create(''));

        $this->assertEquals($category->id, $data['id']);
        $this->assertEquals(0, $data['average']);
    }

    /** @test */
    public function it_shows_average_with_data(): void
    {
        $category = Category::factory()->create();

        $tracker1 = Tracker::factory()->create([
            'target_value' => 1,
            'target_score' => 1,
            'overflow' => true,
            'single' => false,
        ]);

        $tracker1->dataPoints()->save(
            DataPoint::factory()->make([
                'date' => Carbon::createFromTimestamp(0),
                'value' => 1,
            ])
        );

        $tracker2 = Tracker::factory()->create([
            'target_value' => 1,
            'target_score' => 2,
            'overflow' => true,
            'single' => false,
        ]);

        $tracker2->dataPoints()->save(
            DataPoint::factory()->make([
                'date' => Carbon::createFromTimestamp(0),
                'value' => 0.5,
            ])
        );

        $category->trackers()->saveMany([$tracker1->refresh(), $tracker2->refresh()]);

        $resource = CategoryFullResource::make($category->refresh());

        $data = $resource->toArray(Request::create(''));

        $this->assertEquals($category->id, $data['id']);
        $this->assertEquals(2, $data['average']);
    }
}
