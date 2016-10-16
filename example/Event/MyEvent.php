<?php

namespace Evolver\Example\Event;

use Evolver\Event\EventInterface;

class MyEvent implements EventInterface
{
    protected $user;
    protected $ip;
    private $moreStuff = 'somethingDefault';
    private $otherStuff;
    public $stuff;
    
    public function __construct($user, $ip)
    {
        $this->user = $user;
        $this->ip = $ip;
    }
}
