<?php

namespace NiclasVanEyk\TransactionalControllers;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Route;

/**
 * Runs the route action inside a transaction if it is annotated with
 * {@link Transactional}.
 *
 * @internal
 */
final class TransactionalControllerDispatcher extends ControllerDispatcher
{
    public function dispatch(Route $route, $controller, $method)
    {
        $parameters = $this->resolveClassMethodDependencies(
            $route->parametersWithoutNulls(),
            $controller,
            $method
        );

        $attribute = TransactionalControllerReflector::attribute($route);
        if ($attribute === null) {
            return $this->callAction($controller, $method, $parameters);
        }

        return $this->connection($attribute)->transaction(
            fn () => $this->callAction($controller, $method, $parameters),
        );
    }

    /** @see ControllerDispatcher::dispatch() */
    private function callAction($controller, $method, $parameters)
    {
        if (method_exists($controller, 'callAction')) {
            return $controller->callAction($method, $parameters);
        }

        return $controller->{$method}(...array_values($parameters));
    }

    /**
     * Resolves the database connection to start the transaction on.
     *
     * @param Transactional $attribute
     * @return ConnectionInterface
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function connection(Transactional $attribute): ConnectionInterface
    {
        /** @var ConnectionResolverInterface $resolver */
        $resolver = resolve(ConnectionResolverInterface::class);

        return $resolver->connection($attribute->connection);
    }
}
