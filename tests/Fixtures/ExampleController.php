<?php

namespace NiclasVanEyk\TransactionalControllers\Tests\Fixtures;

use NiclasVanEyk\TransactionalControllers\Transactional;

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
