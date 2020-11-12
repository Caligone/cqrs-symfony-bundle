<?php

namespace CQRS\Middleware;

use CQRS\Command\CommandResponseInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class EventsDispatcher implements MiddlewareInterface
{
    protected MessageBusInterface $eventBus;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;   
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $envelope = $stack->next()->handle($envelope, $stack);
        $handledStamp = $envelope->last(HandledStamp::class);
        if (!($handledStamp instanceof HandledStamp)) {
            return $envelope;
        }
        $result = $handledStamp->getResult();
        if (!($result instanceof CommandResponseInterface)) {
            return $envelope;
        }
        foreach ($result->getEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
        return $envelope;
    }
}