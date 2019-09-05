<?php
namespace Joesama\Pintu\Routings;

use Illuminate\Support\Str;
use Illuminate\Routing\Router;
use Joesama\Pintu\Routings\Builder;
use Joesama\Pintu\Components\Manager as Component;

class Manager
{

    /**
     * Routing builder.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Construct RoutesManager
     *
     * @param Router $router
     * @param ServiceProvider $componentProvider
     *
     * @return void
     */
    public function __construct(Component $component)
    {
        $this->builder = new Builder($component);
    }

    /**
     * Build the routing.
     *
     * @return void
     */
    public function routingRegistration(Router $router)
    {
        $this->builder->routing($router);
    }
}
