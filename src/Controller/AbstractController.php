<?php

namespace CQRS\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

abstract class AbstractController
{
    protected DenormalizerInterface $denormalizer;
    protected DecoderInterface $decoder;

    public function __construct(
        DenormalizerInterface $denormalizer,
        DecoderInterface $decoder
    ) {
        $this->denormalizer = $denormalizer;
        $this->decoder = $decoder;
    }

    protected function getParameters(Request $request)
    {
        return array_merge(
            $request->query->all(),
            $this->decoder->decode($request->getContent(), $request->getContentType()),
            $request->attributes->get('_route_params'),
        );
    }
}
