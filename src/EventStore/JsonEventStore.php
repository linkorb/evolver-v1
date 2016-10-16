<?php

namespace Evolver\EventStore;

use Evolver\Event\EventMessage;
use Evolver\Event\EventMessageStream;
use Evolver\Event\EventBusInterface;
use Evolver\EventSourcing\EventSourcingEntityInterface;
use Evolver\Serializer\Serializer;

class JsonEventStore implements EventStoreInterface
{
    protected $basePath;
    protected $serializer;
    protected $eventBus;
    
    public function __construct($basePath, EventBusInterface $eventBus)
    {
        $this->basePath = $basePath;
        $this->eventBus = $eventBus;
        $this->serializer = new Serializer();
    }

    public function commit(EventSourcingEntityInterface $entity)
    {
        $stream = new EventMessageStream($entity->getUncommittedEventMessages());
        
        $this->append($stream);
    }
    
    private function append(EventMessageStream $stream)
    {
        foreach ($stream->getMessages() as $message) {
            $path = $this->basePath . '/' .
                str_replace('\\', '.', $message->getEntityType()) .
                '/' . $message->getEntityId();
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
                
            $filename = $path . '/' . $message->getEntityVersion() . '.json';
            $data = [];
            $data['eventType'] = get_class($message->getEvent());
            $data['entityType'] = $message->getEntityType();
            $data['entityId'] = $message->getEntityId();
            $data['entityVersion'] = $message->getEntityVersion();
            $data['stamp'] = $message->getStamp();
            $data['payload'] = $this->serializer->serialize($message->getEvent());
            $data['metadata'] = $message->getMetadata();
            $json = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
            file_put_contents($filename, $json);
        }
        
        foreach ($stream->getMessages() as $message) {
            $this->eventBus->publish($message);
        }
    }
    
    public function load($entityType, $entityId)
    {
        $path = $this->basePath . '/' .
            str_replace('\\', '.', $entityType) .
            '/' . $entityId;
        $filenames = glob($path . '/*.json');
        
        $entity = $this->serializer->deserialize($entityType, []);
        $entity->setId($entityId);
        
        $stream = new EventMessageStream();
        foreach ($filenames as $filename) {
            $json = file_get_contents($filename);
            $data = json_decode($json, true);
            
            $eventClass = $data['eventType'];
            $event = $this->serializer->deserialize($eventClass, $data['payload']);
            $message = new EventMessage(
                $entityType,
                $entityId,
                $data['entityVersion'],
                $event,
                $data['stamp'],
                $data['metadata']
            );
            $stream->addMessage($message);
        }
        $entity->initializeState($stream);
        return $entity;
    }
}
