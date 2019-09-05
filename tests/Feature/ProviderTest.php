<?php
namespace Tests;

use Joesama\Pintu\PintuProvider;
use Orchestra\Testbench\TestCase;
use Joesama\Pintu\Consoles\ComponentGenerator;

class ProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [PintuProvider::class];
    }

    /** @test */
    public function testServiceProvider()
    {
        $this->assertClassHasAttribute('defer', PintuProvider::class);

        app()->resolveProvider(PintuProvider::class)->commands([ComponentGenerator::class]);

    }
    
}
