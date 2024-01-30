<?php

namespace Tests\Unit\Listeners;

use Tests\TestCase;
use App\Models\Category;
use Mockery\MockInterface;
use App\Events\CategoryUpdated;
use App\Listeners\WipeCategoryMosaic;
use Illuminate\Support\Facades\Event;
use App\Services\Mosaic\CategoryMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class WipeCategoryMosaicTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_listens_to_events(): void
    {
        Event::assertListening(CategoryUpdated::class, WipeCategoryMosaic::class);
    }

    /** @test */
    public function it_process_event(): void
    {
        $category = Category::factory()->create();

        /** @var CategoryMosaicService|MockInterface $mosaic */
        $mosaicService = $this->mock(CategoryMosaicService::class);
        $mosaicService
            ->expects('wipeData')
            ->with($category);

        $listener = new WipeCategoryMosaic($mosaicService);

        $listener->handle(new CategoryUpdated($category));
    }
}
