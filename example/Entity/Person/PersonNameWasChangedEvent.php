<?php

namespace Evolver\Example\Entity\Person;

use Evolver\Event\EventInterface;

class PersonNameWasChangedEvent implements EventInterface
{
    protected $firstname;
    protected $lastname;
    
    public function __construct($firstname, $lastname)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }
    
    public function getFirstname()
    {
        return $this->firstname;
    }
    
    public function getLastname()
    {
        return $this->lastname;
    }
}
