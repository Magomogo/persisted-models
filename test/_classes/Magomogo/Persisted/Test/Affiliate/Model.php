<?php

namespace Magomogo\Persisted\Test\Affiliate;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\ModelInterface;

class Model implements ModelInterface
{
    /**
     * @var Properties
     */
    private $properties;

    public function __construct($properties)
    {
        $this->properties = $properties;
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

    public function save($container)
    {
        return $this->properties->putIn($container);
    }

    public function name()
    {
        return $this->properties->name;
    }

    public function cookie()
    {
        return $this->properties->cookie;
    }
}
