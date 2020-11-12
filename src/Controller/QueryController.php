<?php

namespace CQRS\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class QueryController extends AbstractController
{
    protected MessageBusInterface $queryBus;

    public function __construct(
        DenormalizerInterface $denormalizer,
        DecoderInterface $decoder,
        NormalizerInterface $normalizer,
        MessageBusInterface $queryBus
    ) {
        parent::__construct($denormalizer, $decoder);
        $this->normalizer = $normalizer;
        $this->queryBus = $queryBus;
    }

    public function performQuery(Request $request)
    {
        $queryClass = $request->attributes->get('_query');
        $query = $this->denormalizer->denormalize(
            $this->getParameters($request),
            $queryClass,
        );

        $envelope = $this->queryBus->dispatch($query);
        $handledStamp = $envelope->last(HandledStamp::class);

        if (!($handledStamp instanceof HandledStamp)) {
            throw new \RuntimeException('Command not handled');
        }
        return new JsonResponse(
            $this->normalizer->normalize($handledStamp->getResult()),
            200,
        );
    }
}