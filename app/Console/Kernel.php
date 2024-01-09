<?php

namespace App\Console;

use App\Console\Commands\CleanEmptyDays;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\ComputeAverageDay;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(CleanEmptyDays::class)->dailyAt('00:00');

        $schedule->command(ComputeAverageDay::class)->dailyAt('00:10');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        include base_path('routes/console.php');
    }
}
