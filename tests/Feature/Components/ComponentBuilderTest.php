<?php

namespace Tests\Feature\Components;

use ReflectionClass;
use Illuminate\Routing\Router;
use Joesama\Pintu\PintuProvider;
use Orchestra\Testbench\TestCase;
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

        $this->testNameSpace = new ReflectionClass(PintuProvider::class);
    }

    /**
     * @test
     * @testdox  Validate the class builder exist
     */
    public function theComponentServicesIsExist()
    {
        $file = realpath(__DIR__ . '/../../../src/Services/ComponentServices.php');

        $this->assertFileExists($file);
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
