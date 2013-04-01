<?php
namespace Magomogo\Persisted;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\Container\Memory;

/**
 * @property string $id
 */
abstract class PropertyBag implements \IteratorAggregate
{
    private $id;
    private $origin;
    private $properties;
    private $foreigners;

    public function __construct($id = null, $valuesToSet = null)
    {
        $this->id = $id;
        $this->properties = (object)$this->properties();
        $this->foreigners = (object)$this->foreigners();

        if (!is_null($valuesToSet)) {
            foreach ($valuesToSet as $name => $value) {
                $this->$name = $value;
            }
        }
    }

    protected abstract function properties();

    protected function foreigners()
    {
        return array();
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
     * @param \Magomogo\Persisted\Container\ContainerInterface $container
     */
    public function persisted($id, $container)
    {
        $this->id = $id;
        $this->origin = get_class($container);
    }

    /**
     * @param \Magomogo\Persisted\Container\ContainerInterface $container
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
     * @param \Magomogo\Persisted\Container\ContainerInterface $container
     * @return \Magomogo\Persisted\PropertyBag
     * @throws Exception\Origin
     */
    public function assertOriginIs($container)
    {
        if ($this->isPersistedIn($container)) {
            return $this;
        }
        throw new Exception\Origin();
    }

    public function foreign()
    {
        return $this->foreigners;
    }

    public function __clone()
    {
        $this->properties = clone $this->properties;
        $this->foreigners = clone $this->foreigners;

        foreach ($this->properties as $name => $value) {
            $this->$name = clone $value;
        }

        foreach ($this->foreigners as $name => $value) {
            $this->$name = clone $value;
        }
    }
}
