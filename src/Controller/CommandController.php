<?php

namespace CQRS\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommandController
{
    public function performCommand(Request $request)
    {
        return new Response('YES');
    }
}