<?php
namespace Magomogo\Model;
use Magomogo\Model\PropertyContainer\ContainerInterface;

interface ContainerReadyInterface
{
    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom($container, $id);

    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @return string unique identifier
     */
    public function putIn($container);

    /**
     * @param PropertyContainer\ContainerInterface $container
     * @return void
     */
    public function deleteFrom($container);

    /**
     * Confirms that properties has correct origin
     *
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @return \Magomogo\Model\PropertyBag
     */
    public function propertiesFrom($container);
}
