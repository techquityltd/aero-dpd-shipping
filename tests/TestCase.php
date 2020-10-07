<?php

namespace Tests;

use Aero\Common\Providers\BootingServiceProvider;
use DummyNamespace\Providers\ServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            BootingServiceProvider::class,
            ServiceProvider::class
        ];
    }
}
