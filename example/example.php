<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Evolver\Example\Event\MyEvent;
use Evolver\Example\Entity\Person\Person;
use Evolver\Example\Projector\PdoPersonProjector;

use Evolver\Event\EventStream;
use Evolver\Event\EventBus;
use Evolver\EventStore\JsonEventStore;
use Evolver\EventStore\PdoEventStore;
use Evolver\Serializer\Serializer;

$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();

$username = getenv('EVOLVER_PDO_USERNAME');
$password = getenv('EVOLVER_PDO_PASSWORD');
$address = getenv('EVOLVER_PDO_ADDRESS');
$database = getenv('EVOLVER_PDO_DATABASE');
if (!$username || !$password || !$address || !$database) {
    throw new InvalidArgumentException("Evolver has not been configured correctly. Please refer to the README.md file");
}

$pdo = new PDO("mysql:host=127.0.0.1;dbname=evolver", $username, $password);

$eventbus = new EventBus();
$listener = new PdoPersonProjector($pdo, 'person', 'id');
$eventbus->subscribe($listener);
//$store = new JsonEventStore('/tmp/evolver', $eventbus);
$store = new PdoEventStore($pdo, $eventbus);

$serializer = new Serializer();

$e1 = new MyEvent('joe', '127.0.0.1');
$data = $serializer->serialize($e1);
print_r($data);
$e2 = $serializer->deserialize(MyEvent::class, $data);
print_r($e2);
//$store->append();


$someId = 'MKSNne_8RI2RezhDIfjTAg';
$person = new Person($someId);
$person->setId($someId);
$person->changeName('Joe', 'Johnson');
$person->changeEmail('joe@johnson.web');
$person->changeName('Jack', 'Jackson');
$person->changeEmail('jack@jackson.web');

print_r($person);

$store->commit($person);

echo "RESTORING\n";
$person2 = $store->load(Person::class, $someId);
$person2->changeEmail('jack@gmail.com');
$store->commit($person2);
print_r($person2);

//echo $person2->getEntityVersion() . "\n";
exit("B00m\n");
//print_r($stream);
