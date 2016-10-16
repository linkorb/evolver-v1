<?php

namespace Evolver\EventSourcing;

use Evolver\Event\EventInterface;
use Evolver\Event\EventMessage;
use Evolver\Event\EventMessageStream;

trait EventSourcingTrait
{
    protected $id;
    protected $uncommittedEventMessages = [];
    protected $entityVersion = -1;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    public function getUncommittedEventMessages()
    {
        return $this->uncommittedEventMessages;
    }
    
    public function initializeState(EventMessageStream $stream)
    {
        foreach ($stream->getMessages() as $message) {
            $this->entityVersion++;
            $this->handle($message->getEvent());
        }
    }
    
    public function trigger(EventInterface $event)
    {
        $this->handle($event);
        $this->entityVersion++;
        $message = new EventMessage(static::class, $this->getId(), $this->entityVersion, $event, time(), []);
        $this->uncommittedEventMessages[] = $message;
    }
    
    public function handle(EventInterface $event)
    {
        $className = explode('\\', get_class($event));
        $method = 'on' . end($className);
        if (!method_exists($this, $method)) {
            return;
        }
        
        $this->$method($event);
    }
}
