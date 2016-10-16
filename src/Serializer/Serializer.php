<?php

namespace Evolver\Serializer;

use ReflectionClass;
use ReflectionObject;

class Serializer
{
    public function serialize($obj)
    {
        $data = [];
        $reflectionObject = new ReflectionObject($obj);
        
        foreach ($reflectionObject->getProperties() as $p) {
            $p->setAccessible(true);
            // TODO: Sanity check if value is int/string/etc. If it's an object: recurse or fail?
            $data[(string)$p->getName()] = (string)$p->getValue($obj);
        }
        return $data;
    }
    
    public function deserialize($className, $data)
    {
        $reflectionClass = new ReflectionClass($className);
        $obj = $reflectionClass->newInstanceWithoutConstructor();
        $reflectionObject = new ReflectionObject($obj);
        foreach ($data as $key => $value) {
            if (!$reflectionObject->hasProperty($key)) {
                throw new SerializerException("No such property: `" . $key . '` on class `' . $className . '`');
            }
            $p = $reflectionObject->getProperty($key);
            $p->setAccessible(true);
            $p->setValue($obj, $value);
        }
        return $obj;
    }
}
