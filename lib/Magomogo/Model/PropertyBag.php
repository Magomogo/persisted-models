<?php
namespace Magomogo\Model;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Magomogo\Model\PropertyContainer\Memory;
use Magomogo\Model\Exception\UnknownReference;

/**
 * @property string $id
 */
abstract class PropertyBag implements \IteratorAggregate
{
    private $id;
    private $origin;
    private $properties;
    private $references;

    abstract protected function properties();

    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom($container, $id)
    {
        $properties = new static($id);
        $container->loadProperties($properties);
        return $properties;
    }

    public function __construct($id = null, array $references = array(), $valuesMap = null)
    {
        $this->id = $id;
        $this->properties = (object)$this->properties();
        $this->references = (object)$references;

        if (!is_null($valuesMap)) {
            foreach ($valuesMap as $name => $value) {
                if (isset($this->properties->$name)) {
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
}
