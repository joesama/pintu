<?php
namespace Joesama\Pintu\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Joesama\Pintu\Routings\Manager;
use Illuminate\Support\ServiceProvider;
use Joesama\Pintu\Services\ComponentServices;
use Joesama\Pintu\Components\Manager as Component;

class RoutingServices
{
    /**
     * Component manager.
     *
     * @var Joesama\Pintu\Routings\Manager
     */
    protected $routerManager;

    /**
     * Component manager.
     *
     * @var Joesama\Pintu\Components\Manager
     */
    protected $componentManager;

    /**
     * Construct routing services.
     *
     * @param class $namespace
     */
    public function __construct(ServiceProvider $provider)
    {
        $this->componentManager = new Component($provider);
    }

    /**
     * Generate router for component.
     *
     * @param Router $router
     *
     * @return void
     */
    public function router(Router $router): void
    {
        $this->routerManager = new Manager($this->componentManager);

        $this->routerManager->routingRegistration($router);
    }
}
