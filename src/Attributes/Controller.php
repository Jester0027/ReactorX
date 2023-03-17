<?php

namespace ReactorX\Attributes;

use ReactorX\Scope;

/**
 * Declares a class as a controller with a request scoped lifetime in the DI container
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
readonly class Controller extends Component
{
    public function __construct(
        public string $path = ''
    )
    {
        parent::__construct(Scope::Request);
    }
}