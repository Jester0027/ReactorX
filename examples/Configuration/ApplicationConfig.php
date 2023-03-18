<?php

namespace ReactorX\Examples\Configuration;

use ReactorX\Attributes\Component;
use ReactorX\Attributes\Configuration;
use ReactorX\DependencyInjection\Scope;
use ReactorX\Examples\Services\ConfigurationInterface;
use ReactorX\Examples\Services\FooService;
use ReactorX\Examples\Services\BogusPasswordHasher;
use ReactorX\Examples\Services\PasswordHasherInterface;

/**
 * This class is instantiated once during startup and is used for configuration.<br>
 * Other dependencies can be injected since it is part of the service container
 */
#[Configuration]
final class ApplicationConfig
{
    public function __construct(private readonly FooService $fooService)
    {
    }

    /**
     * This method registers the <code>SomeServiceInterface<code> in the service container with the return value of the method as the implementation.<br><br>
     *
     * The service is then injected by the return type of the configuration method as shown below:
     * <code>
     * class OtherService
     * {
     *     public function __construct(private SomeServiceInterface someService)
     *     {
     *     }
     *
     *     public function returnStuff(): string
     *     {
     *         return $this->someService->doStuff();
     *     }
     * }
     * </code>
     */
    #[Component(Scope::Singleton)]
    public function setupConfiguration(): ConfigurationInterface
    {
        return new class($this->fooService) implements ConfigurationInterface {
            public function __construct(private readonly FooService $fooService)
            {
                var_dump("configuration service constructed");
            }

            public function getConfiguration(): array
            {
                return [
                    'connection_string' => "sqlite:./data.sqlite",
                    'foo_service' => $this->fooService->bar()
                ];
            }
        };
    }

    #[Component]
    public function setupPasswordHasher(): PasswordHasherInterface
    {
        return new BogusPasswordHasher();
    }
}