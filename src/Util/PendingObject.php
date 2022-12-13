<?php

namespace GroupThr3e\AJExchange\Util;

use ReflectionClass;

class PendingObject
{
    public mixed $reference;
    public ReflectionClass $reflectionClass;

    /**
     * @param mixed $reference
     * @param ReflectionClass $reflectionClass
     */
    public function __construct(mixed &$reference, ReflectionClass $reflectionClass)
    {
        $this->reference = &$reference;
        $this->reflectionClass = $reflectionClass;
    }


}