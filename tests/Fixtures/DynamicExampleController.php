<?php

namespace NiclasVanEyk\TransactionalControllers\Tests\Fixtures;

class DynamicExampleController
{
    public function callAction($method, $parameters)
    {
        return $method;
    }

    public function test()
    {
        throw new \Exception("This should not be reached!");
    }
}
