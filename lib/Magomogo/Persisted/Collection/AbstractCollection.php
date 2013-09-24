<?php

namespace Magomogo\Persisted\Collection;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\AbstractProperties;
use Magomogo\Persisted\ModelInterface;

abstract class AbstractCollection implements \ArrayAccess, \IteratorAggregate, \Countable
{
    protected $items = array();

    /**
     * @param AbstractProperties $properties
     * @return ModelInterface
     */
    abstract protected function constructModel($properties);

    /**
     * @return AbstractProperties
     */
    abstract public function constructProperties();

    /**
     * @param ContainerInterface $container
     * @param OwnerInterface $owner
     * @return $this
     */
    public function loadFrom($container, $owner)
    {
        $this->items = array();
        foreach ($container->listReferences($this, $owner) as $properties) {
            /** @var AbstractProperties $properties */
            $this->appendProperties($properties, $properties->id($container));
        }
        return $this;
    }

    /**
     * @param ContainerInterface $container
     * @param OwnerInterface $owner
     * @return $this
     */
    public function putIn($container, $owner)
    {
        $container->referToMany($this, $owner, $this->items);
        return $this;
    }

    /**
     * @param AbstractProperties $properties
     * @param mixed $offset
     */
    public function appendProperties($properties, $offset = null)
    {
        if (is_null($offset)) {
            $this->items[] = $properties;
        } else {
            $this->items[$offset] = $properties;
        }
    }

    public function count()
    {
        return count($this->items);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * @param mixed $offset
     * @return ModelInterface
     */
    public function offsetGet($offset)
    {
        return $this->constructModel($this->items[$offset]);
    }

    /**
     * @param mixed $offset
     * @param MemberInterface $value
     */
    public function offsetSet($offset, $value)
    {
        $value->appendToCollection($this, $offset);
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->asArray());
    }

    /**
     * @return ModelInterface[]
     */
    public function asArray()
    {
        $models = array();
        foreach ($this->items as $offset => $properties) {
            $models[$offset] = $this->constructModel($properties);
        }
        return $models;
    }
}
