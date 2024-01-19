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
            return '<?php echo "<div class=\'h-100\' id=\'app\' data-component=\''.$componentName.'\' data-props=\'" . json_encode($__data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) .  "\'></div>"; ?>';
        });
    }
}
