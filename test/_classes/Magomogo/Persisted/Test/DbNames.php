<?php

namespace Magomogo\Persisted\Test;

use Magomogo\Persisted\Container\SqlDb\NamesInterface;
use Magomogo\Persisted\PropertyBag;
use Magomogo\Persisted\AbstractCollection;

class DbNames implements NamesInterface
{

    /**
     * @param PropertyBag $propertyBag
     * @return string
     */
    public function propertyBagToName($propertyBag)
    {
        return self::personAndEmployeeShareSameTable(self::uniqueName($propertyBag));
    }

    /**
     * @param string $name
     * @return PropertyBag
     */
    public function nameToPropertyBag($name)
    {
        $className = '\\Magomogo\\Persisted\\Test\\' . ucfirst($name) . '\\Properties';
        return new $className;
    }

    public function manyToManyRelationName($propertyBagCollection, $ownerPropertyBag)
    {
        return 'person2keymarker';
    }

    /**
     * @param AbstractCollection $propertyBagCollection
     * @return string
     */
    public function propertyBagCollectionToName($propertyBagCollection)
    {
        return 'keymarker';
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