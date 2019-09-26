<?php

namespace Joesama\Pintu\Routings;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Routing\Router;
use Joesama\Pintu\Routings\Concerns\Grammar;

class Builder
{
    use Grammar;

    /**
     * Array of component.
     *
     * @var Illuminate\Routing\Router
     */
    private $router;

    /**
     * Array of methods.
     *
     * @var array
     */
    private $componentMethods = ['GET', 'POST', 'PUT'];

    /**
     * Construct routing builder.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Component routing registration.
     *
     * @param Collection $component
     *
     * @return void
     */
    public function componentRouting(Collection $components, string $namespace)
    {
        $components->each(function ($component, $controller) use ($namespace) {
            collect($component)->each(function ($attributes, $function) use ($controller, $namespace) {
                $this->router->group(['namespace' => $namespace, 'prefix' => $controller], function (Router $router) use ($controller, $function, $attributes) {
                    collect($this->componentMethods)->each(function ($type) use ($router, $controller, $function, $attributes) {
                        $router->addRoute(
                            Str::upper($type),
                            $this->pathConvention($type, $function, $attributes),
                            $this->classConvention($type, $controller, $function)
                        )->named(
                            $this->namedConvention($controller, $function, $attributes)
                        );
                    });
                });
            });
        });
    }

    /**
     * API routing registration.
     *
     * @param Collection $api
     *
     * @return void
     */
    public function apiRouting(Collection $apis, string $namespace)
    {
        $this->router->group(['namespace' => $namespace, 'prefix' => 'api'], function (Router $router) use ($apis) {
            $apis->each(function ($routes, $method) use ($router) {
                if (!empty($routes)) {
                    list($path, $controller, $named) = Arr::first($routes);

                    $router->addRoute(
                        Str::upper($method),
                        $path,
                        Str::ucfirst($controller)
                    )->named(
                        'api.' . $named
                    );
                }
            });
        });
    }
}
