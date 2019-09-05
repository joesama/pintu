<?php
namespace Joesama\Pintu\Services\Traits;

use Illuminate\Routing\Router;
use Joesama\Pintu\Services\RoutingServices;

trait PintuOrchestraRouting
{

    /**
     * Re build route generator callback.
     *
     * @param  string  $path
     *
     * @return \Closure
     */
    protected function getRouteLoader($path)
    {
        return function (Router $router) use ($path) {
            $routerServices = new RoutingServices($this);

            $routerServices->router($router);

            require $path;
        };
    }
}

