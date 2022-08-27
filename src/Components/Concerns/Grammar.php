<?php

namespace Joesama\Pintu\Components\Concerns;

use ReflectionClass;

trait Grammar
{
    /**
     * Register default controller namespace.
     *
     * @param  ReflectionClass  $reflectionClass
     * @return array
     */
    public function registerNamespace(ReflectionClass $reflectionClass): array
    {
        return [
            'component' => $reflectionClass->getNamespaceName().'\Http\ComponentControllers',
            'api' => $reflectionClass->getNamespaceName().'\Http\ApiControllers',
        ];
    }
}
