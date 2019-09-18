<?php
namespace Joesama\Pintu\Services\Traits;

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
        return function (\Illuminate\Routing\Router $router) use ($path) {
            $routerServices = new \Joesama\Pintu\Services\RoutingServices($this);

            $routerServices->router($router);
            
            require $path;
        };
    }
}

