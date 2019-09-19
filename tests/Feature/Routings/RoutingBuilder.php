<?php

namespace Tests\Feature\Routings;

use Mockery as mock;
use ReflectionClass;
use Illuminate\Support\Arr;
use Illuminate\Routing\Router;
use PHPUnit\Framework\TestCase;
use Joesama\Pintu\PintuProvider;
use Joesama\Pintu\Services\RoutingServices;
use Illuminate\Contracts\Foundation\Application;

class RoutingBuilder extends TestCase
{
    protected function tearDown(): void
    {
        mock::close();
    }

    /** @test */
    public function callRoutingServices()
    {
        $app = mock::mock(Application::class);

        $serviceProvider = new PintuProvider($app);

        $routerServices = new RoutingServices($serviceProvider);

        $this->assertInstanceOf(RoutingServices::class, $routerServices);
    }

    /** @test */
    public function getRouting()
    {
        $app = mock::mock(Application::class);

        $router = mock::mock(Router::class);

        $router->shouldReceive('group')->once();

        $provider = new ReflectionClass(PintuProvider::class);

        $properties = $provider->getDefaultProperties();

        $namespace = Arr::get($properties, 'namespace', null);

        RoutingServices::router($provider, $router, $namespace);
    }
}
