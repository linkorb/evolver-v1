<?php

namespace Evolver\Event;

class EventMessageStream
{
    protected $messages;
    
    public function __construct($messages = [])
    {
        $this->messages = $messages;
    }
    
    public function addMessage(EventMessage $message)
    {
        $this->messages[] = $message;
    }
    
    public function getMessages()
    {
        return $this->messages;
    }
}
