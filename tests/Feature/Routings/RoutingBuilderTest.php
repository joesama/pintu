<?php

namespace Tests\Feature\Routings;

use Mockery as mock;
use Illuminate\Routing\Router;
use Orchestra\Testbench\TestCase;
use Joesama\Pintu\PintuProvider;
use Joesama\Pintu\Services\RoutingServices;

class RoutingBuilderTest extends TestCase
{
    protected function tearDown(): void
    {
        mock::close();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->app = $app;

        $this->router = $app->make(Router::class);
    }

    /** @test */
    public function callRoutingServices()
    {
        $service = mock::mock(RoutingServices::class);

        $service->shouldReceive('router')->with(PintuProvider::class, $this->router)->once();

        $this->assertEquals(null, $service->router(PintuProvider::class, $this->router));
    }

    /** @test */
    public function initiateRouteService()
    {
        $this->assertEquals(0, $this->router->getRoutes()->count());

        RoutingServices::router(PintuProvider::class, $this->router);

        $this->assertNotEquals(0, $this->router->getRoutes()->count());
    }
}
