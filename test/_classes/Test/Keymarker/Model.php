<?php
namespace Test\Keymarker;

use Magomogo\Persisted\PersistedAbstract;

class Model extends PersistedAbstract
{
    /**
     * @param \Magomogo\Persisted\Container\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom($container, $id)
    {
        return new self($container->loadProperties(new Properties($id)));
    }

    /**
     * @param Properties $properties
     */
    public function __construct($properties)
    {
        $this->properties = $properties;
    }

    public function __toString()
    {
        return $this->properties->id;
    }
}
