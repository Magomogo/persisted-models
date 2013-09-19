<?php

namespace Magomogo\Persisted\Test;

use Magomogo\Persisted\Container\SqlDb\NamesInterface;
use Magomogo\Persisted\AbstractProperties;
use Magomogo\Persisted\Collection;

class DbNames implements NamesInterface
{

    /**
     * @param AbstractProperties $properties
     * @return string
     */
    public function propertiesToName($properties)
    {
        return self::personAndEmployeeShareSameTable(self::uniqueName($properties));
    }

    /**
     * @param string $name
     * @return AbstractProperties
     */
    public function nameToProperties($name)
    {
        $className = '\\Magomogo\\Persisted\\Test\\' . ucfirst($name) . '\\Properties';
        return new $className;
    }

    public function manyToManyRelationName($collection, $ownerProperties)
    {
        return 'person2keymarker';
    }

    /**
     * @param Collection\AbstractCollection $collection
     * @return string
     */
    public function collectionToName($collection)
    {
        return 'keymarker';
    }

//----------------------------------------------------------------------------------------------------------------------

    /**
     * @param AbstractProperties $properties
     * @return string
     */
    private function uniqueName($properties)
    {
        $name = strtolower(str_replace('\\', '_', get_class($properties)));
        return preg_replace('/^magomogo_persisted_test_([a-z]+)_properties$/i', '$1', $name);
    }

    private function personAndEmployeeShareSameTable($name)
    {
        return $name === 'employee' ? 'person' : $name;
    }

}