<?php
namespace Magomogo\Persisted\Test\Company;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\PossessionInterface;
use Magomogo\Persisted\Test\JobRecord;
use Magomogo\Persisted\Test\Person;

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

    public function __construct($properties)
    {
        $this->properties = $properties;
    }

    public function name()
    {
        return $this->properties->name;
    }

    /**
     * @param PossessionInterface $properties
     * @param null|string $relationName
     * @return Properties
     */
    public function isOwner($properties, $relationName = null)
    {
        return $properties->ownedBy($this->properties, $relationName);
    }
}
