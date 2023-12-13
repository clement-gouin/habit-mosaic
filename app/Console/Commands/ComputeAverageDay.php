<?php

namespace App\Console\Commands;

use App\Models\Tracker;
use Illuminate\Console\Command;
use App\Services\TrackerService;

class ComputeAverageDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:average';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compute each trackers average day';

    /**
     * Execute the console command.
     */
    public function handle(TrackerService $trackerService): void
    {
        Tracker::all()->each(fn (Tracker $tracker) => $trackerService->updateAverage($tracker));
    }
}
