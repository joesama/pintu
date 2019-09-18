<?php
namespace Joesama\Pintu\Routings;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Joesama\Pintu\Routings\Concerns\Grammar;
use Joesama\Pintu\Components\Manager as ComponentManager;

class Builder
{
    use Grammar;

    /**
     * Array of component.
     *
     * @var Collection
     */
    private $componentCollection;

    /**
     * Array of API
     *
     * @var Collection
     */
    private $apiCollection;

    /**
     * Array of methods.
     *
     * @var array
     */
    private $componentMethods = ['GET','POST', 'PUT'];

    /**
     * Construct routing builder.
     *
     * @param ComponentManager $component
     */
    public function __construct(ComponentManager $component)
    {
        $this->componentCollection = $component->getComponent();

        $this->apiCollection = $component->getApi();
    }

    /**
     * Generate component & API routings.
     *
     * @param Router $router
     *
     * @return void
     */
    public function routing(Router $router)
    {
        $this->componentRouting($router);

        $this->apiRouting($router);
    }

    /**
     * Component routing registration.
     *
     * @param Router $router
     *
     * @return void
     */
    private function componentRouting(Router $router)
    {
        // $this->componentMethods = Router::$verbs;

        $this->componentCollection->each(function ($component, $controller) use ($router) {
            collect($component)->each(function ($attributes, $function) use ($controller, $router) {
                $router->group(['prefix' => $controller], function (Router $router) use ($controller, $function, $attributes) {
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
     * @param Router $router
     *
     * @return void
     */
    private function apiRouting(Router $router)
    {
        $this->apiCollection->each(function ($routes, $method) use ($router) {
            if (! empty($routes)) {
                list($path, $controller, $named) = Arr::first($routes);
                $router->addRoute(
                    Str::upper($method),
                    $path,
                    Str::ucfirst($controller)
                )->named(
                    $named
                );
            }
        });
    }
}
