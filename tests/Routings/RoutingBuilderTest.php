<?php

namespace Joesama\Pintu\Tests\Feature\Routings;

use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Joesama\Pintu\Components\Manager as ComponentManager;
use Joesama\Pintu\PintuProvider;
use Joesama\Pintu\Routings\Builder;
use Joesama\Pintu\Routings\Manager;
use Joesama\Pintu\Services\RoutingServices;
use Mockery as mock;
use Orchestra\Testbench\TestCase;
use ReflectionClass;

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
     * @test
     * @testdox  Validate routing manager.
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
     * @test
     * @testdox  Validate builder naming % path convention with keymap empty array.
     */
    public function theBuilderShouldUseGrammarPathConventionEmptyArray()
    {
        $builder = mock::mock(Builder::class, [$this->router]);

        $type = 'type';

        $controller = 'controller';

        $function = 'function';

        // pathConvention test with empty array
        $pathConventionExpected = $function;

        $builder->shouldReceive('pathConvention')
            ->with($type, $function, [])
            ->andReturn($pathConventionExpected);

        $this->assertIsString($builder->pathConvention($type, $function, []));

        $this->assertEquals($pathConventionExpected, $builder->pathConvention($type, $function, []));
    }

    /**
     * @test
     * @testdox  Validate builder naming % path convention with keymap id referrencing.
     */
    public function theBuilderShouldUseGrammarPathConventionKeymapWithId()
    {
        $builder = mock::mock(Builder::class, [$this->router]);

        $type = 'type';

        $controller = 'controller';

        $function = 'function';

        // pathConvention test with keymap with id referrencing
        $pathConventionKeymapIdMappedExpected = $function.'/{id}';

        $keymap = ['keymap' => [
            $type => 'id',
        ]];

        $builder->shouldReceive('pathConvention')
            ->with($type, $function, $keymap)
            ->andReturn($pathConventionKeymapIdMappedExpected);

        $path = $builder->pathConvention($type, $function, $keymap);

        $this->assertIsString($path);

        $this->assertEquals(
            $pathConventionKeymapIdMappedExpected,
            $path
        );
    }

    /**
     * @test
     * @testdox  Validate builder naming % path convention with empty array keymap.
     */
    public function theBuilderShouldUseGrammarPathConventionKeymapWithEmptyArray()
    {
        $builder = mock::mock(Builder::class, [$this->router]);

        $type = 'type';

        $controller = 'controller';

        $function = 'function';

        // pathConvention test with keymap with empty array
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
    }

    /**
     * @test
     * @testdox  Validate builder naming % path convention with string keymap.
     */
    public function theBuilderShouldUseGrammarPathConventionKeymapWithString()
    {
        $builder = mock::mock(Builder::class, [$this->router]);

        $type = 'type';

        $controller = 'controller';

        $function = 'function';

        // pathConvention test with string keymap
        $pathConventionKeymapExpected = $function.'/{id_name}';

        $keymap = ['keymap' => 'id_name'];

        $builder->shouldReceive('pathConvention')
            ->with($type, $function, $keymap)
            ->andReturn($pathConventionKeymapExpected);

        $this->assertIsString($builder->pathConvention($type, $function, $keymap));

        $this->assertEquals($pathConventionKeymapExpected, $builder->pathConvention($type, $function, $keymap));
    }

    /**
     * @test
     * @testdox  Validate builder naming % class convention .
     */
    public function theBuilderShouldUseGrammarClassConvention()
    {
        $builder = mock::mock(Builder::class, [$this->router]);

        $type = 'type';

        $controller = 'controller';

        $function = 'function';
        // classConvention test
        $classConventionExpected = Str::ucfirst($controller).'Controller@'.Str::camel(Str::lower($type).'_'.$function);

        $builder->shouldReceive('classConvention')
            ->with($type, $controller, $function)
            ->andReturn($classConventionExpected);

        $this->assertIsString($builder->classConvention($type, $controller, $function));

        $this->assertEquals($classConventionExpected, $builder->classConvention($type, $controller, $function));
    }

    /**
     * @test
     * @testdox  Validate builder naming % name convention .
     */
    public function theBuilderShouldUseGrammarNamedConvention()
    {
        $builder = mock::mock(Builder::class, [$this->router]);

        $type = 'type';

        $controller = 'controller';

        $function = 'function';
        // namedConvention test
        $namedConventionExpected = Str::lower($controller.'.'.$type.'.'.$function);

        $builder->shouldReceive('namedConvention')
            ->with($type, $controller, $function, [])
            ->andReturn($namedConventionExpected);

        $this->assertIsString($builder->namedConvention($type, $controller, $function, []));

        $this->assertEquals($namedConventionExpected, $builder->namedConvention($type, $controller, $function, []));
    }

    /**
     * @test
     * @testdox  Validate builder naming % path convention working fine.
     */
    public function theBuilderShouldUseGrammarConvention()
    {
        $builder = mock::mock(Builder::class, [$this->router]);

        $type = 'type';

        $controller = 'controller';

        $function = 'function';

        // pathConvention test with empty array
        $pathConventionExpected = $function;

        $builder->shouldReceive('pathConvention')
            ->with($type, $function, [])
            ->andReturn($pathConventionExpected);

        $this->assertIsString($builder->pathConvention($type, $function, []));

        $this->assertEquals($pathConventionExpected, $builder->pathConvention($type, $function, []));

        // pathConvention test with keymap with empty array
        $pathConventionKeymapIdMappedExpected = $function.'/{id}';

        $keymap = ['keymap' => [
            $type => 'id',
        ]];

        $builder->shouldReceive('pathConvention')
            ->with($type, $function, $keymap)
            ->andReturn($pathConventionKeymapIdMappedExpected);

        $this->assertIsString($builder->pathConvention($type, $function, $keymap));

        $this->assertEquals(
            $pathConventionKeymapIdMappedExpected,
            $builder->pathConvention($type, $function, $keymap)
        );

        // pathConvention test with keymap with empty array
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

        // pathConvention test with string keymap
        $pathConventionKeymapExpected = $function.'/{id_name}';

        $keymap = ['keymap' => 'id_name'];

        $builder->shouldReceive('pathConvention')
            ->with($type, $function, $keymap)
            ->andReturn($pathConventionKeymapExpected);

        $this->assertIsString($builder->pathConvention($type, $function, $keymap));

        $this->assertEquals($pathConventionKeymapExpected, $builder->pathConvention($type, $function, $keymap));

        // classConvention test
        $classConventionExpected = Str::ucfirst($controller).'Controller@'.Str::camel(Str::lower($type).'_'.$function);

        $builder->shouldReceive('classConvention')
            ->with($type, $controller, $function)
            ->andReturn($classConventionExpected);

        $this->assertIsString($builder->classConvention($type, $controller, $function));

        $this->assertEquals($classConventionExpected, $builder->classConvention($type, $controller, $function));

        // namedConvention test
        $namedConventionExpected = Str::lower($controller.'.'.$type.'.'.$function);

        $builder->shouldReceive('namedConvention')
            ->with($type, $controller, $function, [])
            ->andReturn($namedConventionExpected);

        $this->assertIsString($builder->namedConvention($type, $controller, $function, []));

        $this->assertEquals($namedConventionExpected, $builder->namedConvention($type, $controller, $function, []));
    }

    /**
     * @test
     * @testdox Register component routing.
     *
     * @return void
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
                    'named' => '',
                ],
            ],
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
     * @test
     * @testdox Register API routing.
     *
     * @return void
     */
    public function theBuilderCanRegisterApiRouting()
    {
        $builder = mock::mock(Builder::class, [$this->router])->makePartial();

        $collection = mock::mock(Collection::class)->makePartial();

        $api = [
            'get' => [
                ['path', 'controller@method', 'named'],
            ],
            'post' => [
                ['path', 'controller@method', 'named'],
            ],
            'put' => [
                ['path', 'controller@method', 'named'],
            ],
            'patch' => [
                ['path', 'controller@method', 'named'],
            ],
            'delete' => [
                ['path', 'controller@method', 'named'],
            ],
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
     * @test
     * @testdox Register Landing Routing.
     *
     * @return void
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
