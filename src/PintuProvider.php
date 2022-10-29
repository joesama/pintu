<?php

namespace Joesama\Pintu;

use Illuminate\Support\ServiceProvider;
use Joesama\Pintu\Consoles\ComponentGenerator;
use Joesama\Pintu\Services\Traits\PintuLaravelRouting;

class PintuProvider extends ServiceProvider
{
    use PintuLaravelRouting;

    /**
     * List all registered commands.
     *
     * @var array
     */
    protected $console = [
        ComponentGenerator::class,
    ];

    /**
     * Register class functionality.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerComponentRouting();

        $this->commands($this->console);
    }
}
