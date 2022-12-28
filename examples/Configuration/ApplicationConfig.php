<?php

namespace Jester0027\Examples\Configuration;

use Jester0027\Examples\Services\FakeCrudService;
use Jester0027\Examples\Services\SomeService;
use Jester0027\Examples\Services\SomeServiceInterface;
use Jester0027\Phuck\Attributes\Component;
use Jester0027\Phuck\Attributes\Configuration;

/**
 * This class is instantiated once during startup and is used for configuration.<br>
 * Other dependencies can be injected since it is part of the service container
 */
#[Configuration]
final class ApplicationConfig
{
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
    #[Component]
    public function configureSomeService(): SomeServiceInterface
    {
        return new SomeService();
    }
}