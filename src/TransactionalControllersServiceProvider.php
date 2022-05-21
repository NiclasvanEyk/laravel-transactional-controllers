<?php

namespace NiclasVanEyk\TransactionalControllers;

use Illuminate\Routing\Contracts\ControllerDispatcher;
use Illuminate\Support\ServiceProvider;

final class TransactionalControllersServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ControllerDispatcher::class, TransactionalControllerDispatcher::class);
    }
}
