<?php

namespace Evolver\EventStore;

use Evolver\Event\EventMessage;
use Evolver\Event\EventMessageStream;
use Evolver\Event\EventBusInterface;
use Evolver\EventSourcing\EventSourcingEntityInterface;
use Evolver\Serializer\Serializer;
use PDO;

class PdoEventStore implements EventStoreInterface
{
    protected $pdo;
    protected $serializer;
    protected $eventBus;
    protected $tablename = 'event_message';
    
    public function __construct(PDO $pdo, EventBusInterface $eventBus)
    {
        $this->pdo = $pdo;
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
            $sql = sprintf(
                'INSERT INTO %s
                (event_type, entity_type, entity_id, entity_version, stamp, event, metadata)
                VALUES(:event_type, :entity_type, :entity_id, :entity_version, :stamp, :event, :metadata)',
                $this->tablename
            );
            $eventJson = json_encode(
                $this->serializer->serialize($message->getEvent()),
                JSON_UNESCAPED_SLASHES
            );
            $metadataJson = json_encode(
                $message->getMetadata(),
                JSON_UNESCAPED_SLASHES
            );
            $statement = $this->pdo->prepare($sql);
            $statement->execute(
                [
                    ':event_type' => get_class($message->getEvent()),
                    ':entity_type' => $message->getEntityType(),
                    ':entity_id' => $message->getEntityId(),
                    ':entity_version' => $message->getEntityVersion(),
                    ':stamp' => $message->getStamp(),
                    ':event' => $eventJson,
                    ':metadata' => $metadataJson
                ]
            );
        }
        
        foreach ($stream->getMessages() as $message) {
            $this->eventBus->publish($message);
        }
    }
    
    public function load($entityType, $entityId)
    {
        $sql = sprintf(
            'SELECT * FROM `%s`
                WHERE `entity_type` = :entity_type
                AND `entity_id` = :entity_id
                ORDER BY entity_version',
            $this->tablename,
            $entityType,
            $entityId
        );
        $statement = $this->pdo->prepare($sql);
        $statement->execute(
            [
                ':entity_type' => $entityType,
                ':entity_id' => $entityId
            ]
        );
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
    
        $entity = $this->serializer->deserialize($entityType, []);
        $entity->setId($entityId);
        
        
        $stream = new EventMessageStream();
        foreach ($rows as $data) {
            $eventClass = $data['event_type'];
            $event = $this->serializer->deserialize($eventClass, json_decode($data['event'], true));
            $message = new EventMessage(
                $entityType,
                $entityId,
                $data['entity_version'],
                $event,
                $data['stamp'],
                json_decode($data['metadata'], true)
            );
            $stream->addMessage($message);
        }
        $entity->initializeState($stream);
        return $entity;
    }
}
