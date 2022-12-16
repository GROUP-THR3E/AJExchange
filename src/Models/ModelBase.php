<?php

namespace GroupThr3e\AJExchange\Models;

use ReflectionClass;
use ReflectionException;

class ModelBase
{
    // This base constructor automatically takes values from the given dbRow and assigns them to the properties of this
    // class. Only properties of primitive types should be set directly. If the property inherits from ModelBase
    // then a new instance of it is created using the same dbRow. Because of this, property names have to be unique
    // between models.
    public function __construct(array $dbRow)
    {
        // Creates a ReflectionClass to check model's properties
        $classReflector = new ReflectionClass($this);
        foreach ($classReflector->getProperties() as $property) {
            $propertyName = $property->getName();

            // Tries creating a ReflectionClass to check if the property inherits ModelBase. If the property is a
            // primitive type an exception is thrown the dbRow is checked to assign a value directly.
            try {
                $propertyReflector = new ReflectionClass($property->getType()->getName());
                $dbRowCheck = $propertyReflector->getMethod('checkDbRow')->getClosure();

                // Checks the properties class, and only creates a new class if the dbRow contains enough data
                if ($propertyReflector->isSubclassOf(ModelBase::class) ) {
                    $this->$propertyName = $dbRowCheck($propertyReflector, $dbRow)
                        ? $propertyReflector->newInstanceArgs([$dbRow])
                        : null;
                }
            } catch (ReflectionException $e) {
                if ($property->getType()->getName() != 'array' && isset($dbRow[$propertyName])) {
                    $this->$propertyName = $dbRow[$propertyName];
                }
            }
        }
    }

    // Checks that all the properties that aren't an array or inherit ModelBase are set present in the dbRow
    protected static function checkDbRow(ReflectionClass $classReflector, array $dbRow): bool
    {
        $primaryKey = lcfirst($classReflector->getShortName()) . 'Id';
        foreach ($classReflector->getProperties() as $property) {
            if ($property->getType()->isBuiltin() && $property->getName() !== $primaryKey
                && $property->getType()->getName() != 'array' && isset($dbRow[$property->getName()])) return true;
        }

        return false;
    }
}