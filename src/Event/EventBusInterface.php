<?php

namespace Evolver\Event;

interface EventBusInterface
{
    public function subscribe(EventMessageHandlerInterface $handler);
    public function publish(EventMessage $message);
}
