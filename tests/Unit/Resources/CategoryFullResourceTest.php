<?php

namespace Tests\Unit\Resources;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Support\Facades\Request;
use App\Http\Resources\StatisticsResource;
use App\Http\Resources\CategoryFullResource;
use App\Services\Mosaic\CategoryMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CategoryFullResourceTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_makes_statistics(): void
    {
        $category = Category::factory()->create();

        $this->mockMosaicServiceStatistics(CategoryMosaicService::class, $category);

        $resource = CategoryFullResource::make($category);

        $data = $resource->toArray(Request::create(''));

        $this->assertEquals($category->id, $data['id']);
        $this->assertInstanceOf(StatisticsResource::class, $data['statistics']);
    }
}
