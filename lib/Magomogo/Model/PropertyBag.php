<?php
namespace Magomogo\Model;
use Magomogo\Model\PropertyContainer\ContainerInterface;

/**
 * @property string $id
 */
abstract class PropertyBag implements \IteratorAggregate
{
    private $id;
    private $origin;
    private $nameToDataMap;

    abstract protected function properties();

    public function __construct($id = null)
    {
        $this->id = $id;
        $this->nameToDataMap = (object)$this->properties();
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

    public function isPersistedIn(ContainerInterface $container)
    {
        return $this->origin === get_class($container);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->nameToDataMap);
    }

    public function assertOriginIs(ContainerInterface $container)
    {
        if ($this->origin === get_class($container)) {
            return $this;
        }
        throw new Exception\Origin();
    }
}
