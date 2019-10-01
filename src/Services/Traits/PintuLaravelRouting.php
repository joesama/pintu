<?php

namespace Joesama\Pintu\Services\Traits;

use Joesama\Pintu\Services\RoutingServices;

trait PintuLaravelRouting
{
    /**
     * Boot the pintu routing component.
     *
     * @return void
     */
    public function registerComponentRouting()
    {
        RoutingServices::router(__CLASS__, $this->app['router']);
    }
}
