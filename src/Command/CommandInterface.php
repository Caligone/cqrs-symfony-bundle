<?php

namespace CQRS\Command;

interface CommandInterface
{
    public function getIdentifier(): string;
}
