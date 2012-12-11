<?php
namespace Model;
use Model\DataContainer\ContainerInterface;

interface ContainerReadyInterface
{
    /**
     * @param \Model\DataContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom(ContainerInterface $container, $id);

    /**
     * @param \Model\DataContainer\ContainerInterface $container
     * @return string unique identifier
     */
    public function putIn(ContainerInterface $container);

    /**
     * Confirms that properties has correct origin
     *
     * @param \Model\DataContainer\ContainerInterface $container
     * @return \Model\PropertyBag
     */
    public function confirmOrigin(ContainerInterface $container);
}
