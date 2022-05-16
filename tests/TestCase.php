<?php

namespace NiclasVanEyk\TransactionalRoutes\Tests;

use NiclasVanEyk\TransactionalRoutes\TransactionalRoutesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [TransactionalRoutesServiceProvider::class];
    }

    public function getEnvironmentSetUp($app)
    {
        config(['database.default' => 'testing']);
        config(['database.connections.other' => config('database.connections.testing')]);
    }
}
