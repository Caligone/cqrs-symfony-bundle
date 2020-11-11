<?php

namespace CQRS\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CommandController extends AbstractController
{
    protected MessageBusInterface $commandBus;

    public function __construct(
        DenormalizerInterface $denormalizer,
        DecoderInterface $decoder,
        MessageBusInterface $commandBus
    ) {
        parent::__construct($denormalizer, $decoder);
        $this->commandBus = $commandBus;
    }

    public function performCommand(Request $request)
    {
        $commandClass = $request->attributes->get('_command');
        $command = $this->denormalizer->denormalize(
            $this->getParameters($request),
            $commandClass,
        );
        $this->commandBus->dispatch($command);
        return new JsonResponse([
            'identifier' => $command->getIdentifier(),
        ], 202);
    }
}