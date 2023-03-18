<?php

namespace ReactorX\Examples\Services;

use ReactorX\Attributes\Component;
use ReactorX\Scope;

/**
 * Class scanning picks up this class from the <code>#[Component]</code> attribute and registers it in the service container
 */
#[Component(Scope::Singleton)]
final class FooService
{
    public function __construct()
    {
        var_dump("foo service constructed");
    }

    public function bar(): array
    {
        return [
            "foo" => "bar"
        ];
    }
}