<?php

namespace ReactorX;

use ReactorX\Attributes\Component;
use ReactorX\Attributes\Configuration;
use ReactorX\Attributes\Controller;
use ReactorX\Attributes\Route;
use ReactorX\Attributes\Service;
use ReactorX\DependencyInjection\Container;
use ReactorX\DependencyInjection\Scope;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

/**
 * @author Paul N. Etienne <paul.ned@outlook.com>
 */
final class ComponentScanner
{
    use ClassScannerTrait;

    public readonly Container $container;
    private array $controllers = [];
    private array $components = [];
    /**
     * @var array<string, array<string, array<string>>> $actionsMap
     * A map of http verbs and paths to controller actions.
     * <br>
     * example:
     * <pre>
     * [
     *      "GET" => [
     *          "/home" => ["App\\HomeController", "index"],
     *          "" => ["App\\LandingPageController", "getLandingPage"]
     *      ],
     *      "POST" => [
     *          "/contact" => ["App\\ContactController", "contact"]
     *      ]
     * ]
     * </pre>
     */
    private array $actionsMap = [];

    /**
     * @var array<ReflectionClass>
     */
    private array $configurationClasses = [];

    public function __construct()
    {
        $this->container = new Container();
    }

    public function getControllers(): array
    {
        return $this->controllers;
    }

    public function getActionsMappings(): array
    {
        return $this->actionsMap;
    }

    public function getComponents(): array
    {
        return $this->components;
    }

    public function scanDirectory(string $dir): void
    {
        $classes = $this->scanClasses($dir);
        $this->mapClasses($classes);
        $this->setupConfiguration();
    }

    /**
     * @param array<ReflectionClass> $classes
     * @return void
     */
    private function mapClasses(array $classes): void
    {
        foreach ($classes as $class) {
            $attributes = $class->getAttributes(Component::class, ReflectionAttribute::IS_INSTANCEOF);
            foreach ($attributes as $attribute) {
                switch ($attribute->getName()) {
                    case Service::class:
                    case Component::class:
                        $this->registerComponent($class, $attribute);
                        break;
                    case Configuration::class:
                        $this->registerConfiguration($class, $attribute);
                        break;
                    case Controller::class:
                        $this->registerController($class, $attribute);
                        break;
                }
            }
        }
    }

    /**
     * Configures #[Controller] annotated classes and adds them to the DI container
     *
     * @param ReflectionClass $class
     * @param ReflectionAttribute<Controller> $attribute
     * @return void
     */
    private function registerController(ReflectionClass $class, ReflectionAttribute $attribute): void
    {
        $this->registerComponent($class, $attribute);
        $this->controllers[] = $class->getName();
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        $controllerAttribute = $class->getAttributes(Controller::class, ReflectionAttribute::IS_INSTANCEOF)[0];
        /** @var Controller $controllerAttributeInstance */
        $controllerAttributeInstance = $controllerAttribute->newInstance();
        $basePath = trim($controllerAttributeInstance->path, '/');
        foreach ($methods as $method) {
            $methodAttributes = $method->getAttributes(Route::class, ReflectionAttribute::IS_INSTANCEOF);
            foreach ($methodAttributes as $attr) {
                /** @var Route $attrInstance */
                $attrInstance = $attr->newInstance();
                $verb = $attrInstance->method;
                $path = trim($attrInstance->path, '/');
                $fullPath = (strlen($basePath) > 0 ? "/$basePath" : '') . (strlen($path) > 0 ? "/$path" : '');
                $this->actionsMap[$verb][$fullPath] = [$class->getName(), $method->getName()];
            }
        }
    }

    /**
     * Adds any #[Component] annotated class to the DI container
     *
     * @param ReflectionClass $class
     * @param ReflectionAttribute<Component> $attribute
     * @return void
     */
    private function registerComponent(ReflectionClass $class, ReflectionAttribute $attribute): void
    {
        /** @var Component $componentAttributeInstance */
        $componentAttributeInstance = $attribute->newInstance();
        $scope = $componentAttributeInstance->scope;
        $this->registerServiceWithScope($class->getName(), $scope);
        $this->components[] = $class->getName();
    }

    private function registerConfiguration(ReflectionClass $class, ReflectionAttribute $attribute): void
    {
        $this->registerComponent($class, $attribute);
        $this->configurationClasses[] = $class;
    }

    /**
     * @param string $serviceName
     * @param Scope $scope
     * @param callable|null $factory
     * @return void
     */
    private function registerServiceWithScope(string $serviceName, Scope $scope, ?callable $factory = null): void
    {
        $factory ??= fn(Container $container) => $container->createInstance($serviceName);
        switch ($scope) {
            case Scope::Singleton:
                $this->container->singleton($serviceName, $factory);
                break;
            case Scope::Request:
                $this->container->request($serviceName, $factory);
                break;
            case Scope::Transient:
                $this->container->transient($serviceName, $factory);
                break;
            default:
                throw new \RuntimeException("Invalid scope '{$scope->value}' for service '{$serviceName}'.");
        }
    }

    /**
     * @return void
     */
    public function setupConfiguration(): void
    {
        foreach ($this->configurationClasses as $configurationClass) {
            $methods = $configurationClass->getMethods(ReflectionMethod::IS_PUBLIC);
            $configuration = $this->container->get($configurationClass->getName());
            foreach ($methods as $method) {
                $attributes = $method->getAttributes(Component::class, ReflectionAttribute::IS_INSTANCEOF);
                $componentAttribute = !empty($attributes) ? $attributes[0] : null;
                if (isset($componentAttribute)) {
                    /** @var Component $attribute */
                    $attribute = $componentAttribute->newInstance();
                    $componentName = $method->getReturnType()->getName();
                    $this->registerServiceWithScope(
                        $componentName,
                        $attribute->scope,
                        fn() => $configuration->{$method->getName()}()
                    );
                }
            }
        }

    }
}