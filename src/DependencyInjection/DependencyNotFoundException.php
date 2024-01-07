<?php

namespace ReactorX\DependencyInjection;

final class DependencyNotFoundException extends DependencyException
{
    public function __construct(string $serviceId)
    {
        parent::__construct("Service '{$serviceId}' not found.");
    }
}