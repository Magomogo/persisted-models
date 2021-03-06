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
    public static function load($container, $id);

    /**
     * @param ContainerInterface $container
     * @return string unique ID
     */
    public function save($container);
}