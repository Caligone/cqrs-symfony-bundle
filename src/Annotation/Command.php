<?php

namespace CQRS\Annotation;

/**
 * Annotation class for @Command()
 * 
 * @Annotation
 */
class Command extends AbstractAnnotation
{
    protected array $methods = ['POST'];
}
