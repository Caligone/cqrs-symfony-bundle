<?php

namespace CQRS\DependencyInjection;

use CQRS\Inventory\CommandsInventory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CommandsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(CommandsInventory::class)) {
            return;
        }

        $definition = $container->findDefinition(CommandsInventory::class);

        $taggedServices = $container->findTaggedServiceIds('cqrs.command');

        foreach ($taggedServices as $className => $_) {
            $definition->addMethodCall('add', [$className]);
        }
    }
}
