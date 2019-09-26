<?php

namespace Joesama\Pintu\Services;

use ReflectionClass;
use Illuminate\Support\Arr;
use Illuminate\Routing\Router;
use Joesama\Pintu\Routings\Manager;
use Joesama\Pintu\Components\Manager as Component;

class RoutingServices
{
    /**
     * Register service provider routing component.
     *
     * @param string $providerNameSpace
     * @param Router $router
     * @param string|null $namespace
     *
     * @return void
     */
    public static function router(string $providerNameSpace, Router $router, ?string $namespace = null): void
    {
        $provider = new ReflectionClass($providerNameSpace);

        if ($namespace === null) {
            $properties = $provider->getDefaultProperties();

            $namespace = Arr::get($properties, 'namespace', \null);
        }

        $componentManager = new Component($provider);

        $routerManager = new Manager($componentManager);

        $routerManager->setNameSpace($namespace);

        $routerManager->routingRegistration($router);
    }
}
