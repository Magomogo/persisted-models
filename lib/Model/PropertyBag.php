<?php
namespace Model;
use Model\DataContainer\ContainerInterface;

/**
 * @property string $id
 */
class PropertyBag implements \IteratorAggregate
{
    private $id;
    private $origin;
    private $nameToDataMap;

    public function __construct(array $nameToDataMap, $id = null)
    {
        $this->nameToDataMap = (object)$nameToDataMap;
        $this->id = $id;
    }

    public function __get($name)
    {
        if ($name == 'id') {
            return $this->id;
        }
        return $this->nameToDataMap->$name;
    }

    public function __set($name, $value)
    {
        $this->nameToDataMap->$name = $value;
    }

    public function persisted($id, ContainerInterface $container)
    {
        $this->id = $id;
        $this->origin = get_class($container);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->nameToDataMap);
    }

    public function confirmOrigin(ContainerInterface $container)
    {
        if ($this->origin === get_class($container)) {
            return $this;
        }
        throw new Exception\Origin();
    }
}
