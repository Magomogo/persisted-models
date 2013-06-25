<?php
namespace Test\Keymarker;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\ModelInterface;
use Test\Person;

class Model implements ModelInterface
{
    /**
     * @var Properties
     */
    private $properties;

    /**
     * @param ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function load($container, $id = null)
    {
        $p = new Properties();
        $p->persisted($id, $container);

        return $p->loadFrom($container)->constructModel();
    }

    public function putIn($container)
    {
        return $this->properties->putIn($container);
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

    /**
     * @param Person\Properties $personProps
     * @return array of Properties
     */
    public static function listProperties(Person\Properties $personProps)
    {
        $list = array();
        /** @var self $keymarker */
        foreach ($personProps->tags as $keymarker) {
            $list[] = $keymarker->properties;
        }
        return $list;
    }
}
