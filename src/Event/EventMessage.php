<?php

namespace Evolver\Event;

use InvalidArgumentException;

final class EventMessage
{
    protected $entityType;
    protected $entityId;
    protected $entityVersion;
    protected $event;
    protected $stamp;
    protected $metadata;
    
    public function __construct(
        $entityType,
        $entityId,
        $entityVersion,
        EventInterface $event,
        $stamp,
        $metadata = []
    ) {
        if (!$entityType) {
            throw new InvalidArgumentException("Argument entityType required");
        }
        if (!$entityId) {
            throw new InvalidArgumentException("Argument entityId required");
        }
        if (!$stamp) {
            throw new InvalidArgumentException("Argument stamp required");
        }
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->entityVersion = $entityVersion;
        $this->event = $event;
        $this->stamp = $stamp;
        $this->metadata = $metadata;
    }
    
    public function getEntityType()
    {
        return $this->entityType;
    }
    
    public function getEntityId()
    {
        return $this->entityId;
    }
    
    public function getEntityVersion()
    {
        return $this->entityVersion;
    }
    
    public function getEvent()
    {
        return $this->event;
    }
    
    public function getStamp()
    {
        return $this->stamp;
    }
    
    public function getMetadata()
    {
        return $this->metadata;
    }
}
