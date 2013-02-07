<?php
namespace Magomogo\Model;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Magomogo\Model\PropertyContainer\Memory;
use Magomogo\Model\Exception\UnknownReference;

/**
 * @property string $id
 */
class PropertyBag implements \IteratorAggregate
{
    private $type;
    private $id;
    private $origin;
    private $properties;
    private $references;

    public function __construct($type, $id = null, array $valuesMap = array(), array $references = array(),
                                $valuesToSet = null)
    {
        $this->type = $type;
        $this->id = $id;
        $this->properties = (object)$valuesMap;
        $this->references = (object)$references;

        if (!is_null($valuesToSet)) {
            foreach ($valuesToSet as $name => $value) {
                $this->$name = $value;
            }
        }
    }

    public function type() {
        return $this->type;
    }

    public function __get($name)
    {
        if ($name == 'id') {
            return $this->id;
        }
        return $this->properties->$name;
    }

    public function __set($name, $value)
    {
        if (isset($this->properties->$name)) {
            $this->properties->$name = $value;
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
        return new \ArrayIterator($this->properties);
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

    public function reference($referenceName)
    {
        if (property_exists($this->references, $referenceName)) {
            return $this->references->$referenceName;
        }
        throw new UnknownReference($referenceName);
    }

    public function exposeReferences()
    {
        return $this->references;
    }

    public function __clone()
    {
        $this->properties = clone $this->properties;
        $this->references = clone $this->references;

        foreach ($this->properties as $name => $value) {
            $this->$name = clone $value;
        }

        foreach ($this->references as $name => $value) {
            $this->$name = clone $value;
        }
    }
}
