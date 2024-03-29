<?php

namespace ReactorX;

use DI\Container;
use ReactorX\Attributes\Component;
use ReactorX\Attributes\Configuration;
use ReactorX\Attributes\Controller;
use ReactorX\Attributes\Route;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use function DI\autowire;

/**
 * @author Paul N. Etienne <paul.ossdev@proton.me>
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

    public function __construct()
    {
        $this->container = new Container();
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
     * @param ReflectionClass $class
     * @param ReflectionAttribute<Component> $attribute
     * @return void
     */
    private function registerComponent(ReflectionClass $class, ReflectionAttribute $attribute): void
    {
        // TODO Set the scope of each component
        $this->container->set($class->getName(), autowire($class->getName()));
        $this->components[] = $class->getName();
    }

    private function registerConfiguration(ReflectionClass $class, ReflectionAttribute $attribute): void
    {
        $this->registerComponent($class, $attribute);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        $configuration = $this->container->get($class->getName());
        foreach ($methods as $method) {
            $componentAttribute = $method->getAttributes(Component::class, ReflectionAttribute::IS_INSTANCEOF)[0];
            if (isset($componentAttribute)) {
                // TODO register method return value in DI container
            }
        }
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
    }
}