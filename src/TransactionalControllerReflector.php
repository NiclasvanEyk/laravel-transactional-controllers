<?php

namespace NiclasVanEyk\TransactionalControllers;

use Illuminate\Routing\Route;
use ReflectionClass;
use ReflectionException;

/**
 * Extracts the {@link Transactional}-attribute out of a {@link Route}.
 *
 * @internal
 */
final class TransactionalControllerReflector
{
    /** @throws ReflectionException */
    public static function attribute(Route $route): ?Transactional
    {
        $controller = $route->getControllerClass();
        $method = $route->getActionMethod();
        $action = (new ReflectionClass($controller))->getMethod($method);

        foreach ($action->getAttributes(Transactional::class) as $attribute) {
            return $attribute->newInstance();
        }

        return null;
    }
}
