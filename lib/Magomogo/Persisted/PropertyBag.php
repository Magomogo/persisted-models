<?php
namespace Magomogo\Persisted;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\Container\Memory;

/**
 * @property string $id
 */
abstract class PropertyBag implements \IteratorAggregate
{
    private $idInContainer = array();
    private $properties;
    private $foreigners;

    public function __construct($valuesToSet = null)
    {
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

    /**
     * @param ContainerInterface $container|null
     * @return string
     */
    public function id($container)
    {
        return array_key_exists(get_class($container), $this->idInContainer) ?
            $this->idInContainer[get_class($container)] : null;
    }

    public function naturalKey()
    {
        return null;
    }

    public function __get($name)
    {
        return $this->properties->$name;
    }

    public function __set($name, $value)
    {
        if (property_exists($this->properties, $name)) {
            $this->properties->$name = $value;
        } else {
            trigger_error('Undefined property: ' . $name, E_USER_NOTICE);
        }
    }

    /**
     * @param $id
     * @param ContainerInterface $container
     */
    public function persisted($id, $container)
    {
        $this->idInContainer[get_class($container)] = $id;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->properties);
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
            $this->$name = is_object($value) ? clone $value : $value;
        }

        foreach ($this->foreigners as $name => $value) {
            $this->foreigners->$name = clone $value;
        }
    }

    public function __isset($name)
    {
        return property_exists($this->properties, $name);
    }

    /**
     * @param ContainerInterface $container
     * @return self
     */
    public function loadFrom($container)
    {
        return $container->loadProperties($this);
    }

    /**
     * @param ContainerInterface $container
     * @return string unique identifier
     */
    public function putIn($container)
    {
        return $container->saveProperties($this)->id($container);
    }

    /**
     * @param ContainerInterface $container
     * @return void
     */
    public function deleteFrom($container)
    {
        $container->deleteProperties(array($this));
    }

    /**
     * @param self $propertyBag
     * @return self
     */
    public function copyTo($propertyBag)
    {
        foreach ($this as $name => $property) {
            $propertyBag->$name = $property;
        }

        foreach($this->foreign() as $referenceName => $referenceProperties) {
            foreach ($referenceProperties as $name => $property) {
                $propertyBag->foreign()->$referenceName->$name = $property;
            }
        }

        $propertyBag->idInContainer = array_merge($propertyBag->idInContainer, $this->idInContainer);

        return $this;
    }
}
