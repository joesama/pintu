<?php

namespace Joesama\Pintu\Tests;

use Joesama\Pintu\Consoles\ComponentGenerator;
use Joesama\Pintu\PintuProvider;
use Orchestra\Testbench\TestCase;

class ProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            PintuProvider::class,
        ];
    }

    /** @test */
    public function testServiceProvider()
    {
        $this->assertClassHasAttribute('console', PintuProvider::class);
    }
}
