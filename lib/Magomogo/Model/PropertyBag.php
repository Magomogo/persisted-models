<?php
namespace Magomogo\Model;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Magomogo\Model\PropertyContainer\Memory;

/**
 * @property string $id
 */
abstract class PropertyBag implements \IteratorAggregate
{
    private $id;
    private $origin;
    private $nameToDataMap;

    abstract protected function properties();

    public function __construct($id = null, $valuesMap = null)
    {
        $this->id = $id;
        $this->nameToDataMap = (object)$this->properties();

        if (!is_null($valuesMap)) {
            foreach ($valuesMap as $name => $value) {
                if (isset($this->nameToDataMap->$name)) {
                    $this->$name = $value;
                }
            }
        }
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
        if (isset($this->nameToDataMap->$name)) {
            $this->nameToDataMap->$name = $value;
        } else {
            trigger_error('Undefined property: ' . $name, E_USER_NOTICE);
        }
    }

    /**
     * @param $id
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     */
    public function persisted($id, $container)
    {
        $this->id = $id;
        $this->origin = get_class($container);
    }

    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @return bool
     */
    public function isPersistedIn($container)
    {
        return ($container instanceof Memory) || ($this->origin === get_class($container));
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->nameToDataMap);
    }

    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @return \Magomogo\Model\PropertyBag
     * @throws Exception\Origin
     */
    public function assertOriginIs($container)
    {
        if ($this->isPersistedIn($container)) {
            return $this;
        }
        throw new Exception\Origin();
    }
}
