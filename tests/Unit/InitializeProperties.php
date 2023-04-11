<?php

namespace SCBPaymentAPI\Tests\Unit;

use ReflectionObject;

trait InitializeProperties
{
    public function setProperties($object, array $values) {
        $reflected_object = new ReflectionObject($object);
        foreach ($values as $key => $value) {
            if(! $reflected_object->hasProperty($key)) {
                continue;
            }
            $property = $reflected_object->getProperty($key);
            $property->setAccessible(true);
            $property->setValue($object, $value);
            $property->setAccessible(false);
        }
    }
}