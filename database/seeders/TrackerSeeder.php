<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tracker;
use App\Models\Category;
use Illuminate\Database\Seeder;

class TrackerSeeder extends Seeder
{
    public function run(): void
    {
        $targetMail = $this->command->ask("Enter user email", 'admin@' . env('APP_HOST', 'test.com'));

        $user = User::whereEmail($targetMail)->first() ?? User::factory(['email' => $targetMail])->create();

        $user->categories()->saveMany(Category::factory(3)->make());

        $user->trackers()->saveMany(Tracker::factory(10)->make([
            'category_id' => fake()->randomElement($user->categories)->id,
        ]));
    }
}
