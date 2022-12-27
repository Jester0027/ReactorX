<?php

namespace Jester0027\Examples\Services;

use Jester0027\Phuck\Attributes\Component;

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