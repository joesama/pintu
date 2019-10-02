<?php

namespace Tests\Feature\Components;

use Exception;
use ReflectionClass;
use Illuminate\Routing\Router;
use Joesama\Pintu\PintuProvider;
use Orchestra\Testbench\TestCase;
use Joesama\Pintu\Components\Manager;
use Illuminate\Contracts\Support\Arrayable;
use Joesama\Pintu\Services\ComponentServices;

class ComponentBuilderTest extends TestCase
{
    protected $services;

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->router = $app->make(Router::class);

        $this->testNameSpace = PintuProvider::class;
    }

    /**
     * @test
     * @testdox  Validate the component service without service provider.
     */
    public function theComponentServicesWithNonServiceProvider()
    {
        $this->expectException(Exception::class);

        $componentServices = new ComponentServices(Router::class);

        $componentpath = $componentServices->getComponentFilePath();
    }

    /**
     * @test
     * @testdox  Validate component file path and stub.
     */
    public function theComponentFilePathAndStub()
    {
        $provider = new ReflectionClass($this->testNameSpace);

        $componentManger = new Manager($provider);

        $componentPath = $componentManger->getComponentFilePath();

        $componentStub = $componentManger->getComponentFileStub();

        $this->assertIsString($componentPath);

        $this->assertIsString($componentStub);
    }

    /**
     * @test
     * @testdox  description
     */
    public function initiateTheComponentServices()
    {
        $componentServices = new ComponentServices($this->testNameSpace);

        $mappedApi = \array_map(
            'strtoupper',
            collect($componentServices->hasApi())->keys()->toArray()
        );

        $this->assertInstanceOf(Arrayable::class, $componentServices);

        $this->assertIsArray($componentServices->toArray());

        $this->assertIsArray($componentServices->hasApi()->toArray());

        $this->assertContains('GET', $mappedApi);

        $this->assertContains('POST', $mappedApi);

        $this->assertContains('PUT', $mappedApi);

        $this->assertContains('PATCH', $mappedApi);

        $this->assertContains('DELETE', $mappedApi);
    }
}
