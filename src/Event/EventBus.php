<?php

namespace Evolver\Event;

class EventBus implements EventBusInterface
{
    protected $handlers = [];
    
    public function subscribe(EventMessageHandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }
    
    public function publish(EventMessage $message)
    {
        foreach ($this->handlers as $handler) {
            $handler->handle($message);
        }
    }
}
