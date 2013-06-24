<?php
namespace Test\Company;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\ModelInterface;

class Model implements ModelInterface
{
    /**
     * @var Properties
     */
    private $properties;

    /**
     * @param string $id
     * @return Properties
     */
    public static function newProperties($id = null)
    {
        return new Properties($id);
    }

    /**
     * @param ContainerInterface $container
     * @return Properties
     */
    public function propertiesFrom($container)
    {
        return $this->properties;
    }

//----------------------------------------------------------------------------------------------------------------------

    public function __construct($properties)
    {
        $this->properties = $properties;
    }

    public function name()
    {
        return $this->properties->name;
    }
}
