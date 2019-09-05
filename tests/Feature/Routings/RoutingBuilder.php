<?php
namespace Tests\Feature\Routings;

use Illuminate\Support\Arr;
use Illuminate\Routing\Router;
use Joesama\Pintu\PintuProvider;
use Orchestra\Testbench\TestCase;
use Joesama\Pintu\Services\RoutingServices;

class RoutingBuilder extends TestCase
{
    
    protected $router;
    
    protected $testNameSpace;

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->router = $app->make(Router::class);

        $this->testNameSpace = new PintuProvider($app);
    }

    /** @test */
    public function callRoutingServices()
    {
        $routerServices = new RoutingServices($this->testNameSpace);

        $routerServices->router($this->router);

        // list($keys, $values) = Arr::divide(collect($this->router->getRoutes())->toArray());
// dd($values);
        // $this->assertContains('GET', $routedArray);

        // $this->assertContains('POST', $routedArray);

        // $this->assertContains('PUT', $routedArray);
        // dd(\get_class_methods($this->router));
    }
}
