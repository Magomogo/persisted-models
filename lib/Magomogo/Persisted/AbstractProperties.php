<?php
namespace Magomogo\Persisted;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\Container\Memory;

/**
 * @property string $id
 */
abstract class AbstractProperties
{
    private $idInContainer = array();

    /**
     * @var \stdClass
     */
    private $owners;

    /**
     * @var \stdClass
     */
    private $collections;

    public function __construct($valuesToSet = null)
    {
        $this->owners = new \stdClass();
        $this->collections = new \stdClass();

        $this->init();

        if (!is_null($valuesToSet)) {
            foreach ($valuesToSet as $name => $value) {
                $this->$name = $value;
            }
        }
    }

    protected function init()
    {

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

    public function naturalKeyFieldName()
    {
        return null;
    }

    public function __set($name, $value)
    {
        trigger_error('Undefined property: ' . $name, E_USER_NOTICE);
    }

    /**
     * @param $id
     * @param ContainerInterface $container
     */
    public function persisted($id, $container)
    {
        $this->idInContainer[get_class($container)] = $id;
    }

    public function __clone()
    {
        foreach ($this as $name => $value) {
            $this->$name = is_object($value) ? clone $value : $value;
        }

        foreach ($this->foreign() as $name => $value) {
            $this->foreign()->$name = clone $value;
        }

        foreach ($this->collections() as $name => $value) {
            $this->collections()->$name = clone $value;
        }
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
        $container->deleteProperties($this);
    }

    public function foreign()
    {
        return $this->owners;
    }

    public function ownedBy($ownerProperties, $relationName)
    {
        $this->owners->$relationName = $ownerProperties;
        return $this;
    }

    public function hasCollection($collection, $relationName)
    {
        $this->collections->$relationName = $collection;
        return $this;
    }

    public function collections()
    {
        return $this->collections;
    }

}
