<?php

namespace CQRS\Routing;

use CQRS\Annotation\AbstractAnnotation;
use CQRS\Annotation\Query;
use CQRS\Inventory\QueriesInventory;
use Doctrine\Common\Annotations\AnnotationReader;

class QueriesLoader extends AbstractLoader
{
    protected const LOADER_NAME = 'queries';
    protected const ROUTE_NAME_PREFIX = 'cqrs_query_';
    protected const CONTROLLER_PATH = 'CQRS\Controller\QueryController::performQuery';
    protected const ANNOTATION_CLASS = Query::class;

    public function __construct(QueriesInventory $inventory)
    {
        parent::__construct($inventory);
        $this->annotationReader = new AnnotationReader();
    }

    protected static function getAdditionnalDefaults(AbstractAnnotation $annotation, string $annotedClass): array
    {
        return [
            '_query' => $annotedClass,
        ];
    }

    protected static function getIntentNameFromClassName(string $longClassName)
    {
        $baseIntentName = parent::getIntentNameFromClassName($longClassName);
        $intentName = preg_replace('/_query/', '', $baseIntentName);

        return $intentName;
    }
}
