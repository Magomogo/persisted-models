<?php

namespace Test;

use Magomogo\Persisted\Container\Db\NamesInterface;
use Magomogo\Persisted\PropertyBag;

class DbNames implements NamesInterface
{

    /**
     * @param PropertyBag $propertyBag
     * @return string
     */
    public function referencedColumnName($propertyBag)
    {
        return self::personAndEmployeeShareSameTable(self::uniqueName($propertyBag));
    }

    /**
     * @param PropertyBag $propertyBag
     * @return string
     */
    public function containmentTableName($propertyBag)
    {
        return self::personAndEmployeeShareSameTable(self::uniqueName($propertyBag));
    }

//----------------------------------------------------------------------------------------------------------------------

    /**
     * @param PropertyBag $propertyBag
     * @return string
     */
    private function uniqueName($propertyBag)
    {
        $name = strtolower(str_replace('\\', '_', get_class($propertyBag)));
        return preg_replace('/^test_([a-z]+)_properties$/i', '$1', $name);
    }

    private function personAndEmployeeShareSameTable($name)
    {
        return $name === 'employee' ? 'person' : $name;
    }
}