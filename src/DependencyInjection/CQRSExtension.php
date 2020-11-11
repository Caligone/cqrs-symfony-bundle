<?php

namespace CQRS\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use CQRS\Command\CommandInterface;
use CQRS\Inventory\CommandsInventory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class CQRSExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config'),
        );
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(CommandInterface::class)
            ->addTag('cqrs.command');
    }
}