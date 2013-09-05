<?php

namespace Magomogo\Persisted\Test;

use Magomogo\Persisted\Container\Db\NamesInterface;
use Magomogo\Persisted\PropertyBag;

class DbNames implements NamesInterface
{

    /**
     * @param PropertyBag $propertyBag
     * @return string
     */
    public function classToName($propertyBag)
    {
        return self::personAndEmployeeShareSameTable(self::uniqueName($propertyBag));
    }

    /**
     * @param string $name
     * @return PropertyBag
     */
    public function nameToClass($name)
    {
        $className = '\\Magomogo\\Persisted\\Test\\' . ucfirst($name) . '\\Properties';
        return new $className;
    }

//----------------------------------------------------------------------------------------------------------------------

    /**
     * @param PropertyBag $propertyBag
     * @return string
     */
    private function uniqueName($propertyBag)
    {
        $name = strtolower(str_replace('\\', '_', get_class($propertyBag)));
        return preg_replace('/^magomogo_persisted_test_([a-z]+)_properties$/i', '$1', $name);
    }

    private function personAndEmployeeShareSameTable($name)
    {
        return $name === 'employee' ? 'person' : $name;
    }

}