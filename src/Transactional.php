<?php

namespace NiclasVanEyk\TransactionalRoutes;

use Attribute;

/**
 * Signals that the annotated endpoint should be executed inside a database
 * transaction.
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Transactional
{
    /**
     * @param string|null $connection The connection to use for the transaction.
     * If no connection is provided explicitly, the default connection will be
     * used.
     */
    public function __construct(public readonly ?string $connection = null)
    {
    }
}
