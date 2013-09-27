<?php
namespace Magomogo\Persisted\Test\Keymarker;

use Magomogo\Persisted\Collection\MemberInterface;
use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\Test\Person;

class Model implements ModelInterface, MemberInterface
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
     * @param string|null $offset
     */
    public function appendToCollection($collection, $offset = null)
    {
        $properties = $this->properties;

        $collection->propertiesOperation(
            function($items) use ($properties, $offset) {
                if (is_null($offset)) {
                    $items[] = $properties;
                } else {
                    $items[$offset] = $properties;
                }
                return $items;
            }
        );
    }
}
