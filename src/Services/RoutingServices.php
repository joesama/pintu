<?php

namespace Joesama\Pintu\Services;

use Illuminate\Routing\Router;
use Joesama\Pintu\Routings\Manager;
use Joesama\Pintu\Components\Manager as Component;
use ReflectionClass;

class RoutingServices
{
    /**
     * Generate router for component.
     *
     * @param ReflectionClass $provider
     * @param Router $router
     * @param string|null $namespace
     *
     * @return void
     */
    public static function router(ReflectionClass $provider, Router $router, ?string $namespace = null): void
    {

        $componentManager = new Component($provider);

        $routerManager = new Manager($componentManager);

        $routerManager->setNameSpace($namespace);

        $routerManager->routingRegistration($router);
    }
}
