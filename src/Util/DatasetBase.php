<?php

namespace GroupThr3e\AJExchange\Util;

use GroupThr3e\AJExchange\Models\ModelBase;
use http\Exception\InvalidArgumentException;
use PDO;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

class DatasetBase
{
    protected PDO $dbHandle;
    private array $pendingObjects = [];
    private static array $createdModels = [];

    public function __construct()
    {
        $this->dbHandle = DatabaseSingleton::getHandle();
    }

    public function createModel(string $className, array $dbRow): mixed
    {
        try {
            $this->createModelInternal(new ReflectionClass($className), $dbRow, $out);
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException("A valid class name must be given");
        }
        return $out;
    }

    // This method automatically takes values from the given dbRow and uses them to call the model's constructor.
    // Only properties of primitive types should be set directly. If the property inherits from ModelBase
    // then a new instance of it is created using the same dbRow. Because of this, property names have to be unique
    // between models.
    private function createModelInternal(ReflectionClass $classReflector, array $dbRow, &$out)
    {
        $paramValues = [];

        // Creates a ReflectionClass to check model's properties
        foreach ($classReflector->getConstructor()->getParameters() as $parameter) {
            $propertyName = $parameter->getName();

            // Tries creating a ReflectionClass to check if the parameter inherits ModelBase. If the parameter is a
            // primitive type an exception is thrown the dbRow is checked to assign a value directly.
            try {
                $parameterReflector = new ReflectionClass($parameter->getType()->getName());

                // Checks the parameter's class, and only creates a new class if the dbRow contains enough data
                if ($parameterReflector->isSubclassOf(ModelBase::class) ) {
                    if ($this->checkDbRow($parameterReflector, $dbRow)) {
                        $pointer = $this->getStoredModel($parameterReflector, $dbRow);
                        $paramValues[] = &$pointer;
                        if ($pointer === null) $this->pendingObjects[] = new PendingObject($pointer, $parameterReflector);
                    } else {
                        $p = null;
                        $paramValues[] = &$p;
                    }
                } else {
                    // TODO - Add basic support for common classes like DateTime
                    $paramValues[] = null;
                }
            } catch (ReflectionException $e) {
                if (isset($dbRow[$propertyName])) {
                    if ($parameter->getType()->getName() != 'array') {
                        $paramValues[] = $dbRow[$propertyName];
                    } else {
                        $paramValues[] = explode(',', $dbRow[$propertyName]);
                    }
                } else $paramValues[] = null;
            }
        }

        // Registers models before resolving references, to prevent infinite loops for 1-1 relationships
        $out = $this->registerModel($classReflector, $paramValues);


        $i = 0;
        foreach ($this->pendingObjects as $object) {
            if ($out === $object->reference) unset($this->pendingObjects[$i]);
            else $this->createModelInternal($object->reflectionClass, $dbRow, $object->reference);
            $i++;
        }
    }

    private function getStoredModel(ReflectionClass $class, array $dbRow): mixed
    {
        $nameParts = explode('\\', $class->getName());
        $className = $nameParts[sizeof($nameParts) - 1];
        $primaryKey = lcfirst($className) . 'Id';
        return self::$createdModels["{$className}__{$dbRow[$primaryKey]}"] ?? null;
    }

    /**
     * Creates and returns a model for the given class and dbRow
     * @param ReflectionClass $class
     * @param array $params
     * @return mixed|object|null
     */
    private function registerModel(ReflectionClass $class, array $params): mixed
    {
        $nameParts = explode('\\', $class->getName());
        $className = $nameParts[sizeof($nameParts) - 1];
        $primaryKey = lcfirst($className) . 'Id';
        $keyIndex = array_search($primaryKey, array_map(function(ReflectionParameter $param) { return $param->getName(); }, $class->getConstructor()->getParameters()));
        try {
            $newModel = $class->newInstanceArgs($params);
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException();
        }

        self::$createdModels["{$className}__{$params[$keyIndex]}"] = $newModel;
        return $newModel;
    }

    // Checks that all the properties that aren't an array or inherit ModelBase are set present in the dbRow
    private function checkDbRow(ReflectionClass $classReflector, array $dbRow): bool
    {
        $nameParts = explode('\\', $classReflector->getName());
        $className = $nameParts[sizeof($nameParts) - 1];
        $primaryKey = lcfirst($className) . 'Id';

        foreach ($classReflector->getProperties() as $property) {
            // Checks if the property, returning false if it is a built-in type, isn't an array and doesn't
            // have a matching key in $dbRow
            if ($property->getType()->isBuiltin() && isset($dbRow[$property->getName()])
                && $property->getName() !== $primaryKey) return true;
        }

        return false;
    }
}