<?php

namespace Evolver\Example\Entity\Person;

use Evolver\EventSourcing\EventSourcingTrait;
use Evolver\EventSourcing\EventSourcingEntityInterface;
use InvalidArgumentException;

class Person implements EventSourcingEntityInterface
{
    protected $firstname;
    protected $lastname;
    protected $email;
    protected $haircolor = 'red'; // example default value
    
    public function __construct($id)
    {
        if (!$id) {
            throw new InvalidArgumentException("ID required");
        }
        $this->id = $id;
        $this->trigger(
            new PersonWasCreatedEvent()
        );
    }

    use EventSourcingTrait;
    
    public function changeName($firstname, $lastname)
    {
        if (($firstname == $this->firstname) && ($lastname == $this->lastname)) {
            return;
        }
        $this->trigger(
            new PersonNameWasChangedEvent($firstname, $lastname)
        );
    }
    
    public function changeEmail($email)
    {
        if ($this->email == $email) {
            return;
        }
        // validate email here
        $this->trigger(
            new PersonEmailWasChangedEvent($email)
        );
    }
    
    protected function onPersonNameWasChangedEvent(PersonNameWasChangedEvent $event)
    {
        $this->firstname = $event->getFirstname();
        $this->lastname = $event->getLastname();
    }
    
    protected function onPersonEmailWasChangedEvent(PersonEmailWasChangedEvent $event)
    {
        $this->email = $event->getEmail();
    }
}
