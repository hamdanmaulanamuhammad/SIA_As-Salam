<?php

namespace App\Providers;

use Blade;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Carbon::setLocale('id');
        Blade::directive('numberToWords', function ($expression) {
            return "<?php echo \App\Http\Controllers\RaporController::numberToWords($expression); ?>";
        });
    }
}
