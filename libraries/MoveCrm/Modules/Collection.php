<?php

namespace MoveCrm\Modules;

use ArrayObject;
use InvalidArgumentException;
use MoveCrm\Traits\ArrayAccess\Offsets;
use Vtiger_ModuleBasic;

class Collection extends ArrayObject
{
    use Offsets;

    /**
     * Create a new instance.
     *
     * @param \Vtiger_ModuleBasic[] $modules
     */
    public function __construct(array $modules)
    {
        if (!self::_isArrayOfModules($modules)) {
            throw new InvalidArgumentException;
        }

        parent::__construct($modules);
    }

    /**
     * Get a copy of the collection as an array indexed by label.
     *
     * @return \Vtiger_ModuleBasic[]
     */
    public function getArrayCopySortedByLabel()
    {
        $modules = $this->getIndexedByLabel();

        $modules->ksort();

        return $modules->getArrayCopy();
    }

    /**
     * Get a copy of the collection object indexed by label.
     *
     * @return self
     */
    public function getIndexedByLabel()
    {
        return $this->getIndexedByProperty('label');
    }

    /**
     * Checks if the provided array only contains module instances.
     *
     * @param  \Vtiger_ModuleBasic[]  $modules
     * @return bool
     */
    private static function _isArrayOfModules(array $modules)
    {
        foreach ($modules as $module) {
            if (!($module instanceof Vtiger_ModuleBasic)) {
                return false;
            }
        }

        return true;
    }
}
