<?php

namespace CQRS\Routing;

use CQRS\Annotation\Command;
use CQRS\Inventory\CommandsInventory;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class CommandsLoader extends Loader
{
    private bool $isLoaded = false;

    protected const ROUTE_NAME_PREFIX = 'cqrs_';

    public function __construct(CommandsInventory $commands)
    {
        $this->commands = $commands;
        $this->annotationReader = new AnnotationReader();
    }

    public function load($resource, string $type = null)
    {
        if (true === $this->isLoaded) {
            throw new \RuntimeException('Do not add the "commands" loader twice');
        }

        $routes = new RouteCollection();

        foreach($this->commands as $commandClass) {
            $reflectionClass = new \ReflectionClass($commandClass);
            $commandAnnotation = $this->annotationReader->getClassAnnotation($reflectionClass, Command::class);
            if (!$commandAnnotation) {
                continue;
            }
            $defaults = [
                '_controller' => 'CQRS\Controller\CommandController::performCommand',
                '_command' => $commandClass,
                ...$commandAnnotation->getDefaults(),
            ];
            $route = new Route(
                $commandAnnotation->getPath(),
                $defaults,
                $commandAnnotation->getRequirements(),
                $commandAnnotation->getOptions(),
                $commandAnnotation->getHost(),
                $commandAnnotation->getSchemes(),
                $commandAnnotation->getMethods(),
                $commandAnnotation->getCondition(),
            );
            $routes->add(
                $commandAnnotation->getName() ?? $this->getRouteNameFromClassName($commandClass),
                $route,
                $commandAnnotation->getPriority(),
            );
        }

        $this->isLoaded = true;

        return $routes;
    }

    protected function getRouteNameFromClassName(string $longClassName)
    {
        // Remove the namespace
        $shortClassName = preg_replace('/(.*\\\\)/', '', $longClassName);
        // CamelCase to snake_case conversion from https://stackoverflow.com/a/35719689
        return static::ROUTE_NAME_PREFIX . strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $shortClassName));
    }

    public function supports($resource, string $type = null)
    {
        return 'commands' === $type;
    }
}