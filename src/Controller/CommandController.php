<?php

namespace CQRS\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

class CommandController
{
    protected MessageBusInterface $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function performCommand(Request $request)
    {
        $commandClass = $request->attributes->get('_command');
        $command = new $commandClass();
        $this->commandBus->dispatch($command);
        return new JsonResponse([
            'identifier' => $command->getIdentifier(),
        ], 202);
    }
}