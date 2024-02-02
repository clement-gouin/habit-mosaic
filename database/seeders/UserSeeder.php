<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserToken;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (App::environment('production')) {
            return;
        }

        $user = User::factory()->create();

        $token = new UserToken([
            'expires_at' => Carbon::now()->addDay(),
            'token' => bin2hex(openssl_random_pseudo_bytes(20)),
        ]);

        $user->tokens()->save($token);

        $this->command->info(route('login.token', $token->token));

        $this->call(UserBaseTrackersSeeder::class, parameters: ['user' => $user]);
    }
}
