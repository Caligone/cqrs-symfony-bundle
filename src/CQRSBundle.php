<?php

namespace CQRS;

use CQRS\DependencyInjection\CommandsPass;
use CQRS\DependencyInjection\QueriesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CQRSBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new CommandsPass());
        $container->addCompilerPass(new QueriesPass());
    }
}
