<?php

namespace Evolver\Example\Entity\Person;

use Evolver\Event\EventInterface;

class PersonEmailWasChangedEvent implements EventInterface
{
    protected $email;
    
    public function __construct($email)
    {
        $this->email = $email;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
}
