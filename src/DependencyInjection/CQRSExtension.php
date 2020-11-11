<?php

namespace CQRS\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use CQRS\Command\CommandInterface;
use CQRS\Command\CommandHandlerInterface;
use CQRS\Query\QueryInterface;
use CQRS\Query\QueryHandlerInterface;
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
        $container->registerForAutoconfiguration(QueryInterface::class)
            ->addTag('cqrs.query');
        $container->registerForAutoconfiguration(CommandHandlerInterface::class)
            ->addTag('messenger.message_handler', ['bus' => 'command.bus']);
        $container->registerForAutoconfiguration(QueryHandlerInterface::class)
            ->addTag('messenger.message_handler', ['bus' => 'query.bus']);
    }
}