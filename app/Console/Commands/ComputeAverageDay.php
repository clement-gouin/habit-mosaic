<?php

namespace App\Console\Commands;

use App\Models\Tracker;
use App\Services\TrackerService;
use Illuminate\Console\Command;

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
