<?php

namespace Tests\Feature\Listeners;

use App\Events\CategoryUpdated;
use App\Listeners\WipeCategory;
use App\Models\Category;
use App\Services\Mosaic\CategoryMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\TestCase;

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
