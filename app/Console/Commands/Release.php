<?php

namespace App\Console\Commands;

use App\Mail\ReleaseMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class Release extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:release';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute release routine';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Mail::send(new ReleaseMail());
    }
}
