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
     * @return PropertyBag
     */
    public function properties();
}