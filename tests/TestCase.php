<?php

namespace NiclasVanEyk\TransactionalControllers\Tests;

use NiclasVanEyk\TransactionalControllers\TransactionalControllersServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [TransactionalControllersServiceProvider::class];
    }

    public function getEnvironmentSetUp($app)
    {
        config(['database.default' => 'testing']);
        config(['database.connections.other' => config('database.connections.testing')]);
    }
}
