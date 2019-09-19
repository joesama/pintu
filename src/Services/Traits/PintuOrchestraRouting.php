<?php

namespace Joesama\Pintu\Services\Traits;

use ReflectionClass;
use Illuminate\Routing\Router;
use Joesama\Pintu\Services\RoutingServices;

trait PintuOrchestraRouting
{
    /**
     * Build route generator callback.
     *
     * @param  string  $path
     *
     * @return \Closure
     */
    protected function loadRoutes()
    {
        $router = $this->app->make(Router::class);

        $provider = new ReflectionClass(__CLASS__);

        $properties = $provider->getDefaultProperties();

        $namespace = $properties['namespace'];

        RoutingServices::router($provider, $router, $namespace);
    }
}
