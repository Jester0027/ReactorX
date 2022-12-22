<?php

namespace Jester0027\Examples\Services;

use Jester0027\Phuck\Attributes\Component;
use Jester0027\Phuck\Attributes\Scope;

#[Component(Scope::Singleton)]
final class FooService
{
    public function bar(): string
    {
        return 'bar';
    }
}