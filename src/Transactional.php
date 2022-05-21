<?php

namespace NiclasVanEyk\TransactionalControllers;

use Attribute;

/**
 * Signals that the annotated controller method should be executed inside a
 * database transaction.
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Transactional
{
    // The constructor is documented in such detail, since PHPStorm and VS Code
    // show it when hovering over the #[Transactional] attribute above a route
    // action.

    /**
     * The code in this controller method will be executed inside a database
     * transaction.
     *
     * Basically if you see the following code:
     * ```php
     * class MyController
     * {
     *     #[Transactional]
     *     public function store(): string
     *     {
     *         MyModel::create(request()->all());
     *         MyOtherModel::create(request()->all());
     *
     *         return "Done!";
     *     }
     * }
     * ```
     *
     * it is equivalent to
     * ```php
     * class MyController
     * {
     *     public function store(): string
     *     {
     *         return DB::transaction(function () {
     *             MyModel::create(request()->all());
     *             MyOtherModel::create(request()->all());
     *
     *             return "Done!";
     *         });
     *     }
     * }
     * ```
     *
     * just with less nesting and without the need to reference variables such as
     * bound `Model`s from the function parameters via `use` or introducing a double
     * `return` to return values from inside the `transaction` function.
     *
     * For more information visit [the official documentation on GitHub](https://github.com/NiclasvanEyk/laravel-transactional-controllers).
     *
     * @param string|null $connection The connection to use for the transaction.
     * If no connection is provided explicitly, the default connection will be
     * used.
     */
    public function __construct(public readonly ?string $connection = null)
    {
    }
}
