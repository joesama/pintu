<?php

namespace Joesama\Pintu\Services;

use ReflectionClass;
use Illuminate\Support\Collection;
use Joesama\Pintu\Components\Manager;
use Illuminate\Contracts\Support\Arrayable;

class ComponentServices implements Arrayable
{
    /**
     * Component manager.
     *
     * @var Manager
     */
    protected $manager;

    /**
     * Initiate Component Services.
     *
     * @param array $provider
     */
    public function __construct(ReflectionClass $providerReflection)
    {
        $this->manager = new Manager($providerReflection);
    }

    /**
     * Return API collection.
     *
     * @return Collection
     */
    public function hasApi(): Collection
    {
        return $this->manager->getApi();
    }

    /**
     * Return response as collection.
     *
     * @return Collection
     */
    public function toCollection(): Collection
    {
        return $this->manager->getComponent();
    }

    /**
     * Return response as array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->toCollection()->toArray();
    }
}
