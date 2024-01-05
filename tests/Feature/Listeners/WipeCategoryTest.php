<?php

namespace Tests\Feature\Listeners;

use Tests\TestCase;
use App\Models\Tracker;
use App\Models\Category;
use Mockery\MockInterface;
use App\Listeners\WipeTracker;
use App\Events\TrackerUpdated;
use App\Events\CategoryUpdated;
use App\Listeners\WipeCategory;
use Illuminate\Support\Facades\Event;
use App\Services\Mosaic\TrackerMosaicService;
use App\Services\Mosaic\CategoryMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class WipeCategoryTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_listens_to_events(): void
    {
        Event::assertListening(CategoryUpdated::class, WipeCategory::class);
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

        $listener = new WipeCategory($mosaicService);

        $listener->handle(new CategoryUpdated($category));
    }
}
