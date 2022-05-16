<?php

namespace NiclasVanEyk\TransactionalRoutes\Tests\Fixtures;

use NiclasVanEyk\TransactionalRoutes\Transactional;

class ExampleController
{
    #[Transactional]
    public function defaultTransactional()
    {
    }

    public function nonTransactional()
    {
    }

    #[Transactional(connection: 'other')]
    public function otherTransactional()
    {
    }

    #[Transactional]
    public function formRequestTransactional(FailingFormRequest $request)
    {
    }
}
