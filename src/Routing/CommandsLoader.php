<?php

namespace CQRS\Routing;

use CQRS\Annotation\AbstractAnnotation;
use CQRS\Annotation\Command;
use CQRS\Inventory\CommandsInventory;
use Doctrine\Common\Annotations\AnnotationReader;

class CommandsLoader extends AbstractLoader
{
    protected const LOADER_NAME = 'commands';
    protected const ROUTE_NAME_PREFIX = 'cqrs_command_';
    protected const CONTROLLER_PATH = 'CQRS\Controller\CommandController::performCommand';
    protected const ANNOTATION_CLASS = Command::class;

    public function __construct(CommandsInventory $inventory)
    {
        parent::__construct($inventory);
        $this->annotationReader = new AnnotationReader();
    }

    protected static function getAdditionnalDefaults(AbstractAnnotation $annotation, string $annotedClass): array
    {
        return [
            '_command' => $annotedClass,
        ];
    }

    protected static function getIntentNameFromClassName(string $longClassName)
    {
        $baseIntentName = parent::getIntentNameFromClassName($longClassName);
        $intentName = preg_replace('/_command$/', '', $baseIntentName);

        return $intentName;
    }
}
