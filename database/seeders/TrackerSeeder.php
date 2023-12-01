<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tracker;
use Illuminate\Database\Seeder;

class TrackerSeeder extends Seeder
{
    public function run(): void
    {
        $targetMail = $this->command->ask("Enter user email", 'admin@' . env('APP_HOST', 'test.com'));

        $user = User::whereEmail($targetMail)->first() ?? User::factory(['email' => $targetMail])->create();

        $user->trackers()->saveMany(Tracker::factory(10)->make());
    }
}
