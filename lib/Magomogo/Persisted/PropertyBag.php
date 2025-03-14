<?php
namespace Magomogo\Persisted;

use Magomogo\Persisted\Container\ContainerInterface;
use Traversable;

/**
 * @property string $id
 */
abstract class PropertyBag implements \IteratorAggregate
{
    private $idInContainer = array();
    private $properties;

    public function __construct($valuesToSet = null)
    {
        $this->properties = (object)$this->properties();

        if (!is_null($valuesToSet)) {
            foreach ($valuesToSet as $name => $value) {
                $this->$name = $value;
            }
        }

        $this->init();
    }

    protected function init()
    {

    }

    protected abstract function properties();

    /**
     * @param ContainerInterface $container|null
     * @return string
     */
    public function id($container)
    {
        return array_key_exists(get_class($container), $this->idInContainer) ?
            $this->idInContainer[get_class($container)] : null;
    }

    public function resetPersistency()
    {
        $this->idInContainer = array();
        return $this;
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

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->properties);
    }

    public function __clone()
    {
        $this->properties = clone $this->properties;

        foreach ($this->properties as $name => $value) {
            $this->$name = is_object($value) ? clone $value : $value;
        }

        if ($this instanceof PossessionInterface) {
            foreach ($this->foreign() as $name => $value) {
                $this->foreign()->$name = clone $value;
            }
        }
    }

    public function __isset($name)
    {
        return property_exists($this->properties, $name);
    }

    /**
     * @param \Magomogo\Persisted\Container\ContainerInterface $container
     * @param mixed $id properties identifier in the given container
     * @return self
     */
    public function loadFrom($container, $id)
    {
        $this->persisted($id, $container);
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

        if (($this instanceof PossessionInterface) && ($propertyBag instanceof PossessionInterface)) {
            foreach($this->foreign() as $referenceName => $referenceProperties) {
                foreach ($referenceProperties as $name => $property) {
                    $propertyBag->foreign()->$referenceName->$name = $property;
                }
            }
        }

        $propertyBag->idInContainer = array_merge($propertyBag->idInContainer, $this->idInContainer);

        return $this;
    }
}
