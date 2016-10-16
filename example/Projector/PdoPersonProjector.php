<?php

namespace Evolver\Example\Projector;

use Evolver\Event\EventInterface;
use Evolver\Event\EventMessageHandlerInterface;
use Evolver\Projector\AbstractPdoProjector;

class PdoPersonProjector extends AbstractPdoProjector implements EventMessageHandlerInterface
{
    public function onPersonEmailWasChangedEvent($message)
    {
        $id = $message->getEntityId();
        $this->create($id);
        $this->update($id, ['email' => $message->getEvent()->getEmail()]);
    }
    
    public function onPersonNameWasChangedEvent($message)
    {
        $id = $message->getEntityId();
        $event = $message->getEvent();
        $this->create($id);
        $this->update($id, [
            'firstname' => $event->getFirstname(),
            'lastname' => $event->getLastname()
        ]);
    }
}
