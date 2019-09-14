<?php
namespace Joesama\Pintu;

use Illuminate\Support\ServiceProvider;
use Joesama\Pintu\Consoles\ComponentGenerator;
use Joesama\Pintu\Services\Traits\PintuOrchestraRouting;

class PintuProvider extends ServiceProvider
{
    /**
     * List all registered commands.
     *
     * @var array
     */
    protected $console = [
        ComponentGenerator::class
    ];

    /**
     * Register class functionality.
     *
     * @return void
     */
    public function register(): void
    {
        $this->commands($this->console);
    }
}
