<?php

namespace Jester0027\Phuck\Attributes;

use Jester0027\Phuck\Scope;

/**
 * attribute for components instantiated at application startup, mainly responsible for configuration
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
readonly class Configuration extends Component
{
    public function __construct()
    {
        parent::__construct(Scope::Startup);
    }
}