<?php

namespace Joesama\Pintu\Routings;

use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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
     * @param  Router  $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Component routing registration.
     *
     * @param  Collection  $component
     * @return void
     */
    public function componentRouting(Collection $components, string $namespace)
    {
        $options = [
            'namespace' => $namespace,
            'middleware' => ['web'],
        ];

        $components->each(function ($component, $controller) use ($options) {
            collect($component)->each(function ($attributes, $function) use ($controller, $options) {
                $options = Arr::set($options, 'prefix', $controller);

                if (Arr::get($attributes, 'auth', false)) {
                    $middleware = Arr::get($options, 'middleware');

                    $options = Arr::set($options, 'middleware', array_merge(Arr::wrap($middleware), ['auth']));
                }

                $only = Arr::get($attributes, 'only', collect($this->componentMethods)->toArray());

                $componentMethods = collect($this->componentMethods)->filter(function ($item) use ($only) {
                    return \in_array($item, \array_flip(\array_change_key_case(\array_flip($only), \CASE_UPPER)));
                });

                $this->router->group($options, function (Router $router) use ($componentMethods, $controller, $function, $attributes) {
                    $componentMethods->each(function ($type) use ($router, $controller, $function, $attributes) {
                        $router->addRoute(
                            Str::upper($type),
                            $this->pathConvention($type, $function, $attributes),
                            $this->classConvention($type, $controller, $function)
                        )->name(
                            $this->namedConvention($type, $controller, $function, $attributes)
                        );
                    });
                });
            });
        });
    }

    /**
     * Landing routing registration.
     *
     * @param  Collection  $component
     * @return void
     */
    public function landingRouting(string $namespace)
    {
        $options = [
            'namespace' => $namespace,
            'middleware' => ['web', 'guest'],
        ];

        $type = 'GET';
        $controller = 'landing';
        $function = 'default';
        $attributes = [];

        $this->router->group($options, function (Router $router) use ($type, $controller, $function) {
            $router->addRoute(
                Str::upper($type),
                '/',
                $this->classConvention($type, $controller, $function)
            )->name(
                $controller
            );
        });
    }

    /**
     * API routing registration.
     *
     * @param  Collection  $api
     * @return void
     */
    public function apiRouting(Collection $apis, string $namespace)
    {
        $options = [
            'namespace' => $namespace . '\Api',
            'prefix' => 'api',
            'middleware' => ['api'],
        ];

        $this->router->group($options, function (Router $router) use ($apis) {
            $apis->each(function ($routes, $method) {
                if (! empty($routes)) {
                    foreach ($routes as $apiRoute) {
                        [$path, $controller, $named] = $apiRoute;
                        $this->router->addRoute(
                            Str::upper($method),
                            $path,
                            Str::ucfirst($controller)
                        )->name(
                            'api.'.$named
                        );
                    }
                }
            });
        });
    }
}
