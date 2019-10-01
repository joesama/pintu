<?php

namespace Joesama\Pintu\Services\Traits;

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
        RoutingServices::router(__CLASS__, $this->app['router']);
    }
}
