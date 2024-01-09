<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\DayService;
use Illuminate\Console\Command;

class CleanEmptyDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean empty days data';

    /**
     * Execute the console command.
     */
    public function handle(DayService $dayService): void
    {
        User::all()->each(function (User $user) use ($dayService) {
            $count = $dayService->cleanEmptyDays($user);
            $this->info('User ' . $user->id . ': cleaned ' . $count . ' days');
        });
    }
}
