<?php

namespace Joesama\Pintu\Routings;

use Illuminate\Support\Arr;
use Illuminate\Routing\Router;
use Joesama\Pintu\Routings\Builder;
use Joesama\Pintu\Components\Manager as ComponentManager;

class Manager
{
    /**
     * Collection of API definition.
     *
     * @var ComponentManager
     */
    private $component;

    /**
     * Default controller namespaces.
     *
     * @var string
     */
    private $namespace;

    /**
     * Initialize routes manager.
     *
     * @param ComponentManager $component
     */
    public function __construct(ComponentManager $component)
    {
        $this->component = $component;
    }

    /**
     * Build the routing.
     *
     * @return void
     */
    public function routingRegistration(Router $router)
    {
        $builder = new Builder($router);

        $builder->componentRouting($this->component->getComponent(), $this->getComponentNameSpace());

        $builder->apiRouting($this->component->getApi(), $this->getApiNameSpace());
    }

    /**
     * Set default namespace.
     *
     * @param string|null $namespace
     *
     * @return void
     */
    public function setNameSpace(string $namespace = null)
    {
        $this->namespace = $namespace;
    }

    protected function getComponentNameSpace()
    {
        if ($this->namespace === null) {
            return Arr::get($this->component->getComponentNameSpace(), 'component');
        }

        return $this->namespace;
    }

    protected function getApiNameSpace()
    {
        if ($this->namespace === null) {
            return Arr::get($this->component->getComponentNameSpace(), 'api');
        }

        return $this->namespace;
    }
}
