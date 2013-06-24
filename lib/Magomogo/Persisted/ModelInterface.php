<?php

namespace Magomogo\Persisted;

use Magomogo\Persisted\Container\ContainerInterface;

interface ModelInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function load($container, $id = null);

    /**
     * @param ContainerInterface $container
     * @return PropertyBag
     */
    public function propertiesFrom($container);
}