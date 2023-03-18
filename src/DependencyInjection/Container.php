<?php

namespace ReactorX\DependencyInjection;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use RuntimeException;

class Container implements ContainerInterface
{
    use RequestContainerTrait;

    /**
     * @var array<string, mixed>
     */
    protected array $singletons = [];

    /**
     * @var array<string, mixed>
     */
    protected array $requestServices = [];

    /**
     * @var array<string, callable>
     */
    protected array $singletonDefinitions = [];

    /**
     * @var array<string, callable>
     */
    protected array $requestDefinitions = [];

    /**
     * @var array<string, callable>
     */
    protected array $transientDefinitions = [];


    /**
     * Registers a singleton service.
     *
     * @param string $name
     * @param callable|null $factory
     */
    public function singleton(string $name, ?callable $factory = null): void
    {
        if ($factory === null) {
            $factory = fn(Container $container) => $container->createInstance($name);
        }

        $this->singletonDefinitions[$name] = $factory;
    }

    /**
     * Registers a request-scoped service.
     *
     * @param string $name
     * @param callable|null $factory
     */
    public function request(string $name, ?callable $factory = null): void
    {
        if ($factory === null) {
            $factory = fn(Container $container) => $container->createInstance($name);
        }

        $this->requestDefinitions[$name] = $factory;
    }

    /**
     * Registers a transient service.
     *
     * @param string $name
     * @param callable|null $factory
     */
    public function transient(string $name, ?callable $factory = null): void
    {
        if ($factory === null) {
            $factory = fn(Container $container) => $container->createInstance($name);
        }

        $this->transientDefinitions[$name] = $factory;
    }

    /**
     * @throws ReflectionException
     */
    public function createInstance(string $className)
    {
        $class = new ReflectionClass($className);
        $dependencies = $this->resolveDependencies($class);

        return $class->newInstanceArgs($dependencies);
    }

    /**
     * Gets the service instance based on its scope.
     *
     * @param string $id
     * @return mixed
     * @throws RuntimeException
     */
    public function get(string $id): mixed
    {
        if (isset($this->singletonDefinitions[$id])) {
            if (!isset($this->singletons[$id])) {
                $this->singletons[$id] = $this->singletonDefinitions[$id]($this);
            }
            return $this->singletons[$id];
        }

        if (isset($this->requestDefinitions[$id])) {
            return $this->getRequestService($id);
        }

        if (isset($this->transientDefinitions[$id])) {
            return $this->transientDefinitions[$id]($this);
        }

        throw new \RuntimeException("Service '{$id}' not found.");
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $id): bool
    {
        return isset($this->singletonDefinitions[$id]) ||
            isset($this->requestDefinitions[$id]) ||
            isset($this->transientDefinitions[$id]);
    }

    /**
     * Resets the request-scoped services.
     */
    public function resetRequestServices(): void
    {
        unset($this->requestServices);
    }

    /**
     * Gets the request-scoped service instance.
     *
     * @param string $name
     * @return mixed
     */
    protected function getRequestService(string $name): mixed
    {
        if (!isset($this->requestServices[$name])) {
            $this->requestServices[$name] = $this->requestDefinitions[$name]($this);
        }
        return $this->requestServices[$name];
    }

    /**
     * Resolves the dependencies of a given class.
     *
     * @param ReflectionClass $class
     * @return array An array of resolved dependencies
     */
    protected function resolveDependencies(ReflectionClass $class): array
    {
        $constructor = $class->getConstructor();
        $dependencies = [];

        if (null !== $constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $dependencies[] = $this->resolveDependency($parameter);
            }
        }

        return $dependencies;
    }

    /**
     * Resolves a single dependency.
     *
     * @param ReflectionParameter $parameter
     * @return mixed The resolved dependency
     */
    protected function resolveDependency(ReflectionParameter $parameter): mixed
    {
        $dependencyClass = $parameter->getType();

        if (null === $dependencyClass) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw new \RuntimeException("Unable to resolve the '{$parameter->getName()}' parameter.");
        }

        return $this->get($dependencyClass->getName());
    }
}