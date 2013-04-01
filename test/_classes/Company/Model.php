<?php
namespace Company;

use Magomogo\Persisted\ContainerReadyAbstract;

class Model extends ContainerReadyAbstract
{
    /**
     * @param \Magomogo\Persisted\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom($container, $id)
    {
        return new self($container->loadProperties(new Properties($id)));
    }

    public function __construct($properties)
    {
        $this->properties = $properties;
    }

    public function name()
    {
        return $this->properties->name;
    }
}
