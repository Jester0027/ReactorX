<?php

namespace Jester0027\Phuck\Attributes;

use Jester0027\Phuck\Scope;

/**
 * Adds the annotated class/method return value to the DI container with the specified scope
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
readonly class Component
{
    public function __construct(
        public Scope $scope = Scope::Request
    )
    {
    }
}

