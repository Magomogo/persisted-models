<?php
namespace Model;
use Model\DataContainer\ContainerInterface;

interface ContainerReadyInterface
{
    /**
     * @param DataContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom(ContainerInterface $container, $id);

    /**
     * @param DataContainer\ContainerInterface $container
     * @return string unique identifier
     */
    public function putIn(ContainerInterface $container);

    /**
     * Confirms that properties has correct origin
     *
     * @param DataContainer\ContainerInterface $container
     * @return PropertyBag
     */
    public function confirmOrigin(ContainerInterface $container);
}
