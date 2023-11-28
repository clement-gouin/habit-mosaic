<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        Blade::directive('vue', function ($componentName) {
            // phpcs:ignore:Generic.Files.LineLength.TooLong
            return '<?php echo "<div id=\'app\' data-component=\'' . $componentName . '\' data-props=\'" . str_replace("\'", "\\u0027", json_encode($__data)) .  "\'></div>"; ?>';
        });
    }
}
