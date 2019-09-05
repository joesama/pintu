<?php
namespace Joesama\Pintu;

use Illuminate\Support\ServiceProvider;
use Joesama\Pintu\Consoles\ComponentGenerator;

class PintuProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // if ($this->app->runningInConsole()) {
            $this->commands([
                ComponentGenerator::class
            ]);
        // }
    }

    /**
     * Register class functionality.
     *
     * @return void
     */
    public function register(): void
    {
        $this->commands([
            ComponentGenerator::class
        ]);
    }
}
