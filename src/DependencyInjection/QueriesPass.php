<?php

namespace CQRS\DependencyInjection;

use CQRS\Inventory\QueriesInventory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class QueriesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(QueriesInventory::class)) {
            return;
        }

        $definition = $container->findDefinition(QueriesInventory::class);

        $taggedServices = $container->findTaggedServiceIds('cqrs.query');

        foreach ($taggedServices as $className => $_) {
            $definition->addMethodCall('add', [$className]);
        }
    }
}
