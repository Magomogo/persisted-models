<?php
namespace Magomogo\Persisted\Test\Keymarker;

use Magomogo\Persisted\CollectableModelInterface;
use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\Test\Person;

class Model implements ModelInterface, CollectableModelInterface
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
    public static function load($container, $id)
    {
        $p = new Properties();
        return new self($p->loadFrom($container, $id));
    }

    public function save($container)
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
     * @param Collection $collection
     * @param $offset
     */
    public function appendToCollection($collection, $offset)
    {
        $collection->appendPropertyBag($this->properties, $offset);
    }
}
