<?php

namespace Jester0027\Examples\Services;

use Jester0027\Phuck\Attributes\Component;

/**
 * Class scanning picks up this class from the <code>#[Component]</code> attribute and registers it in the service container
 */
#[Component]
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