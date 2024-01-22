<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::share('version', config('app.version'));
    }
}
