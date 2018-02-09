<?php

namespace MoveCrm\Traits;

use ReflectionClass;
use ReflectionProperty;

trait Reflection
{
    /**
     * Return a reflected collection of the public class properties.
     *
     * @return \ReflectionProperty[]
     */
    protected static function _getPublicProperties()
    {
        $reflection = new ReflectionClass(__CLASS__);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        return $properties;
    }
}
