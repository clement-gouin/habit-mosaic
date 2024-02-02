<?php

namespace Database\Seeders;

use App\Models\Tracker;
use App\Services\Mosaic\CategoryMosaicService;
use App\Services\Mosaic\DayMosaicService;
use App\Services\Mosaic\TrackerMosaicService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class RandomDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (App::environment('production')) {
            return;
        }

        $target = Carbon::now()->subMonths(3);

        Tracker::each(function (Tracker $tracker) use ($target) {
            $date = Carbon::now()->subDay();

            while ($date->isAfter($target) || $date->equalTo($target)) {
                $tracker->getDataPointAt($date)->update([
                    'value' => round(fake()->randomFloat(max: $tracker->overflow ? $tracker->target_value * 3 : $tracker->target_value) / $tracker->value_step) * $tracker->value_step,
                ]);

                $date->subDay();
            }

            App::make(TrackerMosaicService::class)->wipeData($tracker);
            App::make(CategoryMosaicService::class)->wipeData($tracker->category);
            App::make(DayMosaicService::class)->wipeData($tracker->user);
        });
    }
}
