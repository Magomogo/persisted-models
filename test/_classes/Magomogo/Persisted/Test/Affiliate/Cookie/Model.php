<?php

namespace Magomogo\Persisted\Test\Affiliate\Cookie;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\ModelInterface;

class Model implements ModelInterface
{
    /**
     * @var Properties
     */
    private $properties;

    /**
     * Model constructor.
     * @param Properties $prop
     */
    public function __construct(Properties $prop)
    {
        $this->properties = $prop;
    }

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

    /**
     * @param ContainerInterface $container
     * @return string unique ID in container
     */
    public function save($container)
    {
        return $this->properties->putIn($container);
    }
}
