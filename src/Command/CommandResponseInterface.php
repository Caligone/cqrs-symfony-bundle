<?php

namespace CQRS\Command;

interface CommandResponseInterface
{
    public function getEvents(): \Generator;
}
