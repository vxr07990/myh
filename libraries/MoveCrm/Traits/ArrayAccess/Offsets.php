<?php

namespace MoveCrm\Traits\ArrayAccess;

use ArrayAccess;
use DomainException;
use InvalidArgumentException;

trait Offsets
{
    /**
     * Get a new collection object indexed by the specified property name.
     *
     * @todo Improve the thrown exception messages.
     *
     * @param  string  $property
     * @return \ArrayAccess
     */
    public function getIndexedByProperty($property)
    {
        if (!($this instanceof ArrayAccess)) {
            throw new DomainException('DOMAIN EXCEPTION!');
        }

        if (!is_string($property)) {
            throw new InvalidArgumentException('INVALID ARGUMENT EXCEPTION!');
        }

        $fqcn = sprintf('\%s', __CLASS__);
        $sorted = new $fqcn([]);

        foreach ($this->getArrayCopy() as $object) {
            if (!property_exists($object, $property)) {
                throw new InvalidArgumentException('INVALID ARGUMENT EXCEPTION!');
            }

            $index = ($property == 'label') ? vtranslate($object->${property})
                                            : $object->${property};

            $sorted->offsetSet($index, $object);
        }

        return $sorted;
    }
}
