<?php

namespace Evolver\EventSourcing;

use Evolver\Event\EventInterface;
use Evolver\Event\EventMessage;
use Evolver\Event\EventMessageStream;

interface EventSourcingEntityInterface
{
    public function getId();
    public function getUncommittedEventMessages();
    public function initializeState(EventMessageStream $stream);
    public function trigger(EventInterface $event);
    public function handle(EventInterface $event);
}
