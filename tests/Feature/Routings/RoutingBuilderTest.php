<?php

namespace Tests\Feature\Routings;

use Mockery as mock;
use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Routing\Router;
use Joesama\Pintu\PintuProvider;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Collection;
use Joesama\Pintu\Routings\Builder;
use Joesama\Pintu\Routings\Manager;
use Joesama\Pintu\Services\RoutingServices;
use Joesama\Pintu\Components\Manager as ComponentManager;

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

    /**
     * @test
     * @testdox Initiate routing service.
     * */
    public function initiateRouteService()
    {
        $this->assertEquals(0, $this->router->getRoutes()->count());

        RoutingServices::router(PintuProvider::class, $this->router);

        $this->assertEquals(0, $this->router->getRoutes()->count());
    }

    /**
     *
     * @test
     * @testdox  Validate routing manager.
     *
     */
    public function theRoutingManagerShouldBuildTheRouting()
    {
        $provider = new ReflectionClass(PintuProvider::class);

        $componentManger = new ComponentManager($provider);

        $namespace = $componentManger->getComponentNameSpace();

        $routingManger = new Manager($componentManger);

        $routingManger->setNameSpace();

        $this->assertEquals($routingManger->getApiNameSpace(), $namespace['api']);

        $this->assertEquals($routingManger->getComponentNameSpace(), $namespace['component']);

        $routingManger->setNameSpace('Controller');

        $this->assertNotEquals($routingManger->getApiNameSpace(), $namespace['api']);

        $this->assertNotEquals($routingManger->getComponentNameSpace(), $namespace['component']);
    }

    /**
     *
     * @test
     * @testdox  Validate builder naming % path convention working fine.
     *
     */
    public function theBuilderShouldUseGrammarConvention()
    {
        $builder = mock::mock(Builder::class, [$this->router]);

        $type = 'type';

        $controller = 'controller';

        $function = 'function';

        $pathConventionExpected = $function;

        $builder->shouldReceive('pathConvention')
            ->with($type, $function, [])
            ->andReturn($pathConventionExpected);

        $this->assertIsString($builder->pathConvention($type, $function, []));

        $this->assertEquals($pathConventionExpected, $builder->pathConvention($type, $function, []));

        $pathConventionKeymapEmptyArrayExpected = $function;

        $keymap = ['keymap' => []];

        $builder->shouldReceive('pathConvention')
            ->with($type, $function, $keymap)
            ->andReturn($pathConventionKeymapEmptyArrayExpected);

        $this->assertIsString($builder->pathConvention($type, $function, $keymap));

        $this->assertEquals(
            $pathConventionKeymapEmptyArrayExpected,
            $builder->pathConvention($type, $function, $keymap)
        );

        $pathConventionKeymapExpected = $function . '/{id_name}';

        $keymap = ['keymap' => 'id_name'];

        $builder->shouldReceive('pathConvention')
            ->with($type, $function, $keymap)
            ->andReturn($pathConventionKeymapExpected);

        $this->assertIsString($builder->pathConvention($type, $function, $keymap));

        $this->assertEquals($pathConventionKeymapExpected, $builder->pathConvention($type, $function, $keymap));

        $classConventionExpected = Str::ucfirst($controller) . 'Controller@' . Str::camel(Str::lower($type) . '_' . $function);

        $builder->shouldReceive('classConvention')
            ->with($type, $controller, $function)
            ->andReturn($classConventionExpected);

        $this->assertIsString($builder->classConvention($type, $controller, $function));

        $this->assertEquals($classConventionExpected, $builder->classConvention($type, $controller, $function));

        $namedConventionExpected = Str::lower($controller . '.' . $type . '.' . $function);

        $builder->shouldReceive('namedConvention')
            ->with($type, $controller, $function, [])
            ->andReturn($namedConventionExpected);

        $this->assertIsString($builder->namedConvention($type, $controller, $function, []));

        $this->assertEquals($namedConventionExpected, $builder->namedConvention($type, $controller, $function, []));
    }

    /**
     *
     * @test
     * @testdox Register component routing.
     * @return void
     *
     */
    public function theBuilderCanRegisterComponentRouting()
    {
        $builder = mock::mock(Builder::class, [$this->router])->makePartial();

        $collection = mock::mock(Collection::class)->makePartial();

        $component = [
            'controll' => [
                'function' => [
                    'keymap' => 'id',
                    'auth' => true,
                    'named' => ''
                ]
            ]
        ];

        $collection->shouldReceive('make')->with($component)->twice()->andReturnSelf();

        $namespace = 'namespace';

        $builder->shouldReceive('componentRouting')
            ->with($collection->make($component), $namespace)
            ->once();

        $builder->componentRouting($collection->make($component), $namespace);

        $build = new Builder($this->router);

        $build->componentRouting(collect($component), $namespace);

        $this->assertCount(3, $this->router->getRoutes());
    }

    /**
     *
     * @test
     * @testdox Register API routing.
     * @return void
     *
     */
    public function theBuilderCanRegisterApiRouting()
    {
        $builder = mock::mock(Builder::class, [$this->router])->makePartial();

        $collection = mock::mock(Collection::class)->makePartial();

        $api = [
            'get' => [
                ['path', 'controller@method', 'named']
            ],
            'post' => [
                ['path', 'controller@method', 'named']
            ],
            'put' => [
                ['path', 'controller@method', 'named']
            ],
            'patch' => [
                ['path', 'controller@method', 'named']
            ],
            'delete' => [
                ['path', 'controller@method', 'named']
            ]
        ];

        $collection->shouldReceive('make')->with($api)->twice()->andReturnSelf();

        $namespace = 'namespace';

        $builder->shouldReceive('apiRouting')
            ->with($collection->make($api), $namespace)
            ->once();

        $builder->apiRouting($collection->make($api), $namespace);

        $build = new Builder($this->router);

        $build->apiRouting(collect($api), $namespace);

        $this->assertCount(5, $this->router->getRoutes());
    }

    /**
     *
     * @test
     * @testdox Register Landing Routing.
     * @return void
     *
     */
    public function theBuilderCanRegisterLandingRouting()
    {
        $builder = mock::mock(Builder::class, [$this->router])->makePartial();

        $namespace = 'namespace';

        $builder->shouldReceive('landingRouting')
            ->with($namespace)
            ->once();

        $builder->landingRouting($namespace);

        $build = new Builder($this->router);

        $build->landingRouting($namespace);

        $this->assertCount(1, $this->router->getRoutes());
    }
}
