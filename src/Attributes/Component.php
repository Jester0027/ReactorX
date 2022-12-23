<?php

namespace Jester0027\Phuck\Attributes;

/**
 * Adds the annotated class to the DI container with the specified scope
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
readonly class Component
{
    public function __construct(
        public Scope $scope = Scope::Request
    )
    {
    }
}

enum Scope: string
{
    case Singleton = "SINGLETON";
    case Request = "REQUEST";
    case Transient = "TRANSIENT";
}