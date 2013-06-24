<?php
namespace Test\Keymarker;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\PropertyBag;

class Model implements ModelInterface
{
    /**
     * @var Properties
     */
    private $properties;

    /**
     * @param string $id
     * @return PropertyBag
     */
    public static function newPropertyBag($id = null)
    {
        return new Properties($id);
    }

    /**
     * @param ContainerInterface $container
     * @return PropertyBag
     */
    public function propertiesFor($container)
    {
        return $this->properties;
    }

//----------------------------------------------------------------------------------------------------------------------

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
