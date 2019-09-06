<?php
namespace Tests\Feature\Routings;

use Mockery as mock;
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

        $serviceProvider = new PintuProvider($app);

        $routerServices = new RoutingServices($serviceProvider);

        $router->shouldReceive('group')->twice();

        $routerServices->router($router);

        $this->assertObjectHasAttribute('componentManager', $routerServices);

        $this->assertObjectHasAttribute('routerManager', $routerServices);

    }
}
