<?php

namespace Evolver\Projector;

use Evolver\Event\EventMessage;
use PDO;

abstract class AbstractPdoProjector
{
    protected $pdo;
    protected $tablename;
    protected $primaryKey;
    
    public function __construct(PDO $pdo, $tablename, $primaryKey = 'id')
    {
        $this->tablename = $tablename;
        $this->pdo = $pdo;
        $this->primaryKey = $primaryKey;
    }
    
    public function handle(EventMessage $message)
    {
        $event = $message->getEvent();
        $className = explode('\\', get_class($event));
        $method = 'on' . end($className);
        if (!method_exists($this, $method)) {
            return;
        }
        
        $this->$method($message);
    }
    
    public function create($id, $data = [])
    {
        $sql = sprintf('SELECT * FROM `%s` WHERE `%s` = :id', $this->tablename, $this->primaryKey, $id);
        $statement = $this->pdo->prepare($sql);
        $statement->execute(
            [
                ':id' => $id
            ]
        );

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows)>0) {
            return;
        }
        
        // Insert it
        $sql = sprintf('INSERT INTO `%s` (%s) VALUES (:id)', $this->tablename, $this->primaryKey);

        $statement = $this->pdo->prepare($sql);
        $statement->execute(
            [
                ':id' => $id
            ]
        );
    }
    
    public function update($id, $data = [])
    {
        foreach ($data as $key => $value) {
            $sql = sprintf('UPDATE `%s` SET %s=:value WHERE %s=:id', $this->tablename, $key, $this->primaryKey);

            $statement = $this->pdo->prepare($sql);
            $statement->execute(
                [
                    ':value' => $value,
                    ':id' => $id
                ]
            );
        }
    }
    
    public function delete($id)
    {
        $sql = sprintf('DELETE FROM `%s` WHERE %s=:id', $this->tablename, $this->primaryKey);

        $statement = $this->pdo->prepare($sql);
        $statement->execute(
            [
                ':id' => $id
            ]
        );
    }
}
