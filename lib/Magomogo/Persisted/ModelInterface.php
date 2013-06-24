<?php

namespace Magomogo\Persisted;

use Magomogo\Persisted\Container\ContainerInterface;

interface ModelInterface
{
    /**
     * @param string $id
     * @return PropertyBag
     */
    public static function newProperties($id = null);

    /**
     * @param ContainerInterface $container
     * @return PropertyBag
     */
    public function propertiesFor($container);
}