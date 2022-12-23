<?php

namespace Jester0027\Phuck;

use DI\Container;
use Jester0027\Phuck\Attributes\Component;
use Jester0027\Phuck\Attributes\Controller;
use ReflectionAttribute;
use ReflectionClass;
use function DI\autowire;

final class ClassScanner
{
    use ClassScannerTrait;

    private Container $serviceContainer;

    public function __construct(string $dir)
    {
        $this->serviceContainer = new Container();
        $classes = $this->scanClasses($dir);
        $this->mapClasses($classes);
    }

    /**
     * @param array<ReflectionClass> $classes
     * @return void
     */
    private function mapClasses(array $classes): void
    {
        foreach ($classes as $class) {
            $attributes = $class->getAttributes();
            foreach ($attributes as $attribute) {
                switch ($attribute->getName()) {
                    case Component::class:
                        $this->registerComponent($class, $attribute);
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
        // TODO Create a map of [path -> controller action] using the attributes applied on both the class and the methods
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
        $this->serviceContainer->set($class->getName(), autowire($class->getName()));
    }
}