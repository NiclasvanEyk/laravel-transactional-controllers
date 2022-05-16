<?php

namespace NiclasVanEyk\TransactionalRoutes;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteAction;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Extracts the {@link Transactional}-attribute out of a {@link Route}.
 *
 * @internal
 */
final class TransactionalRouteReflector
{
    public function __construct(private readonly Route $route)
    {
    }

    /**
     * @throws ReflectionException
     */
    public function attribute(): ?Transactional
    {
        $action = $this->reflect();

        foreach ($action->getAttributes(Transactional::class) as $attribute) {
            return $attribute->newInstance();
        }

        return null;
    }

    /**
     * @throws ReflectionException
     */
    private function reflect(): ReflectionFunction|ReflectionMethod
    {
        if ($this->isControllerAction()) {
            $controller = $this->route->getControllerClass();
            $method = $this->route->getActionMethod();

            return (new ReflectionClass($controller))->getMethod($method);
        }

        $callable = $this->route->getAction('uses');
        if ($this->isSerializedClosure()) {
            $callable = unserialize($callable)->getClosure();
        }

        return new ReflectionFunction($callable);
    }

    /** @see Route::isControllerAction */
    private function isControllerAction(): bool
    {
        return is_string($this->route->getAction('uses'))
            && ! $this->isSerializedClosure();
    }

    /** @see Route::isSerializedClosure() */
    private function isSerializedClosure(): bool
    {
        return RouteAction::containsSerializedClosure($this->route->getAction());
    }
}
