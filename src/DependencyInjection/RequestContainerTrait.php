<?php

namespace ReactorX\DependencyInjection;

/**
 * @author Paul N. Etienne <paul.ned@outlook.com>
 */
trait RequestContainerTrait
{
    public function newRequestScopedContainer(): Container
    {
        return new class($this) extends Container {
            public function __construct(Container $container)
            {
                $this->singletonDefinitions = &$container->singletonDefinitions;
                $this->requestDefinitions = &$container->requestDefinitions;
                $this->transientDefinitions = &$container->transientDefinitions;
                $this->singletons = &$container->singletons;
                $this->requestServices = [];
            }
        };
    }
}