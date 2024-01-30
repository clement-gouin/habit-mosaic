<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\CategoryFullResource;
use App\Http\Resources\StatisticsResource;
use App\Models\Category;
use App\Objects\Statistics;
use App\Services\Mosaic\CategoryMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Request;
use Mockery\MockInterface;
use Tests\TestCase;

class CategoryFullResourceTest extends TestCase
{
    use DatabaseMigrations;

    protected CategoryMosaicService $mosaicServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        /** @var CategoryMosaicService|MockInterface $mock */
        $this->mosaicServiceMock = $this->mock(CategoryMosaicService::class);

        $this->mosaicServiceMock->expects('getStatistics')
            ->andReturn(Statistics::fromDataCollection(collect()));

        $this->app->instance(CategoryMosaicService::class, $this->mosaicServiceMock);
    }

    /** @test */
    public function it_makes_statistics(): void
    {
        $category = Category::factory()->create();

        $resource = CategoryFullResource::make($category);

        $data = $resource->toArray(Request::create(''));

        $this->assertEquals($category->id, $data['id']);
        $this->assertInstanceOf(StatisticsResource::class, $data['statistics']);
    }
}
