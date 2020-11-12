<?php

namespace CQRS\Routing;

use CQRS\Annotation\AbstractAnnotation;
use CQRS\Inventory\AbstractInventory;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

abstract class AbstractLoader extends Loader
{
    protected const LOADER_NAME = null;
    protected const ROUTE_NAME_PREFIX = 'cqrs_';
    protected const CONTROLLER_PATH = null;
    protected const ANNOTATION_CLASS = null;
    private AbstractInventory $inventory;

    private bool $isLoaded = false;

    public function __construct(AbstractInventory $inventory)
    {
        $this->inventory = $inventory;
        $this->annotationReader = new AnnotationReader();
    }

    abstract protected static function getAdditionnalDefaults(AbstractAnnotation $annotation, string $annotedClass): array;

    public function load($resource, string $type = null)
    {
        if (true === $this->isLoaded) {
            throw new \RuntimeException(sprintf('Do not add the "%s" loader twice', static::LOADER_NAME));
        }

        $routes = new RouteCollection();

        foreach ($this->inventory as $class) {
            $reflectionClass = new \ReflectionClass($class);
            $annotation = $this->annotationReader->getClassAnnotation($reflectionClass, static::ANNOTATION_CLASS);
            if (!$annotation) {
                continue;
            }
            $defaults = array_merge(
                ['_controller' => static::CONTROLLER_PATH],
                $annotation->getDefaults(),
                static::getAdditionnalDefaults($annotation, $class),
            );
            $intentName = static::getIntentNameFromClassName($class);
            $route = new Route(
                $annotation->getPath() ?? "/$intentName",
                $defaults,
                $annotation->getRequirements(),
                $annotation->getOptions(),
                $annotation->getHost(),
                $annotation->getSchemes(),
                $annotation->getMethods(),
                $annotation->getCondition(),
            );
            $routes->add(
                $annotation->getName() ?? static::ROUTE_NAME_PREFIX.$intentName,
                $route,
                $annotation->getPriority(),
            );
        }

        $this->isLoaded = true;

        return $routes;
    }

    protected static function getIntentNameFromClassName(string $longClassName)
    {
        // Remove the namespace
        $shortClassName = preg_replace('/(.*\\\\)/', '', $longClassName);
        // CamelCase to snake_case conversion from https://stackoverflow.com/a/35719689
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $shortClassName));
    }

    public function supports($resource, string $type = null)
    {
        return static::LOADER_NAME === $type;
    }
}
