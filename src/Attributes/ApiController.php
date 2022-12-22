<?php

namespace Jester0027\Phuck\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
readonly class ApiController
{
    public function __construct(
        public string $path = ''
    )
    {
    }
}