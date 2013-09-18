<?php

namespace Magomogo\Persisted;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\PropertyBag;
use Magomogo\Persisted\CollectableModelInterface;
use Magomogo\Persisted\CollectionOwnerInterface;

abstract class PropertyBagCollection implements \ArrayAccess, \IteratorAggregate, \Countable
{
    protected $items = array();

    abstract protected function constructModel($propertyBag);

    /**
     * @param ContainerInterface $container
     * @param CollectionOwnerInterface $owner
     * @return $this
     */
    public function loadFrom($container, $owner)
    {
        $this->items = array();
        foreach ($container->listReferences($this, $owner) as $propertyBag) {
            /** @var PropertyBag $propertyBag */
            $this->appendPropertyBag($propertyBag, $propertyBag->id($container));
        }
        return $this;
    }

    /**
     * @param ContainerInterface $container
     * @param CollectionOwnerInterface $owner
     * @return $this
     */
    public function putIn($container, $owner)
    {
        $container->referToMany($this, $owner, $this->items);
        return $this;
    }

    public function appendPropertyBag($propertyBag, $offset = null)
    {
        if (is_null($offset)) {
            $this->items[] = $propertyBag;
        } else {
            $this->items[$offset] = $propertyBag;
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

    public function offsetGet($offset)
    {
        return $this->constructModel($this->items[$offset]);
    }

    /**
     * @param mixed $offset
     * @param CollectableModelInterface $value
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

    public function asArray()
    {
        $models = array();
        foreach ($this->items as $offset => $propertyBag) {
            $models[$offset] = $this->constructModel($propertyBag);
        }
        return $models;
    }
}