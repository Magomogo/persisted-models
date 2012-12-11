<?php
namespace Model;
use Model\PropertyContainer\ContainerInterface;

interface ContainerReadyInterface
{
    /**
     * @param \Model\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom(ContainerInterface $container, $id);

    /**
     * @param \Model\PropertyContainer\ContainerInterface $container
     * @return string unique identifier
     */
    public function putIn(ContainerInterface $container);

    /**
     * Confirms that properties has correct origin
     *
     * @param \Model\PropertyContainer\ContainerInterface $container
     * @return \Model\PropertyBag
     */
    public function confirmOrigin(ContainerInterface $container);
}

trait ContainerUtils
{
    public function putIn(ContainerInterface $container)
    {
        return $container->saveProperties($this->properties)->id;
    }

    public function confirmOrigin(ContainerInterface $container)
    {
        return $this->properties->confirmOrigin($container);
    }
}
