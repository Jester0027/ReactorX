<?php

namespace ReactorX\Attributes;

use ReactorX\DependencyInjection\Scope;

#[\Attribute(\Attribute::TARGET_CLASS)]
readonly class Service extends Component
{
    public function __construct(
        Scope $scope = Scope::Request
    )
    {
        parent::__construct($scope);
    }
}