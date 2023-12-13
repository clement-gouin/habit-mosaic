<?php

namespace Database\Seeders;

use Throwable;
use App\Models\User;
use RuntimeException;
use App\Models\Tracker;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewUserSeeder extends Seeder
{
    public const DATA = [
        [
            'name' => 'Morning',
            'icon' => 'mug-saucer',
            'trackers' => [
                [
                    'name' => 'Sleep',
                    'icon' => 'moon',
                    'single' => false,
                    'unit' => 'hours slept',
                    'target_value' => 8,
                ],
                [
                    'name' => 'Brush teeth',
                    'icon' => 'tooth',
                ],
                [
                    'name' => 'Make bed',
                    'icon' => 'bed',
                ],
                [
                    'name' => 'Aerate room',
                    'icon' => 'wind',
                ],
            ],
        ],
        [
            'name' => 'Day',
            'icon' => 'briefcase',
            'trackers' => [
                [
                    'name' => 'Walk',
                    'icon' => 'shoe-prints',
                    'single' => false,
                    'unit' => 'steps',
                    'value_step' => 1000,
                    'target_value' => 20000,
                ],
                [
                    'name' => 'Drink water',
                    'icon' => 'bottle-water',
                    'single' => false,
                    'unit' => 'litters',
                    'value_step' => 0.25,
                    'target_value' => 1,
                ],
            ],
        ],
        [
            'name' => 'Evening',
            'icon' => 'house-chimney',
            'trackers' => [
                [
                    'name' => 'Read',
                    'icon' => 'book',
                    'single' => false,
                    'unit' => 'chapters',
                    'target_value' => 2,
                ],
                [
                    'name' => 'Alcohol',
                    'icon' => 'martini-glass',
                    'single' => false,
                    'unit' => 'units',
                    'target_value' => 2,
                    'target_score' => -1,
                ],
            ],
        ],
    ];

    public const DEFAULT_TRACKER_DATA = [
        'single' => true,
        'unit' => null,
        'value_step' => 1,
        'target_value' => 1,
        'target_score' => 1,
    ];

    public function run(?User $user = null): void
    {
        try {
            DB::beginTransaction();

            if ($user === null) {
                $user = User::factory()->create();
            }

            $categoryCounter = 1;
            $trackerCounter = 1;

            foreach (self::DATA as $categoryData) {
                $category = new Category([
                    'order' => $categoryCounter++,
                    ...collect($categoryData)->only(['name', 'icon']),
                ]);

                $user->categories()->save($category);

                foreach ($categoryData['trackers'] as $trackerData) {
                    $tracker = new Tracker([
                        'order' => $trackerCounter++,
                        'category_id' => $category->id,
                        ...self::DEFAULT_TRACKER_DATA,
                        ...$trackerData,
                    ]);

                    $user->trackers()->save($tracker);
                }
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            throw new RuntimeException(previous: $e);
        }
    }
}
