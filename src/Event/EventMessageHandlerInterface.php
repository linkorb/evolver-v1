<?php

namespace Evolver\Event;

interface EventMessageHandlerInterface
{
    public function handle(EventMessage $message);
}
