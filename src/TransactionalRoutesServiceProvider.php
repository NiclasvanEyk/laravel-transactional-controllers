<?php

namespace NiclasVanEyk\TransactionalRoutes;

use Illuminate\Routing\Contracts\ControllerDispatcher;
use Illuminate\Support\ServiceProvider;

final class TransactionalRoutesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ControllerDispatcher::class, TransactionalControllerDispatcher::class);
    }
}
