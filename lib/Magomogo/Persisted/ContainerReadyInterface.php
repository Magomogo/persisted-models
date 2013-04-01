<?php
namespace Magomogo\Persisted;
use Magomogo\Persisted\PropertyContainer\ContainerInterface;

interface ContainerReadyInterface
{
    /**
     * @param \Magomogo\Persisted\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom($container, $id);

    /**
     * @param \Magomogo\Persisted\PropertyContainer\ContainerInterface $container
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
     * @param \Magomogo\Persisted\PropertyContainer\ContainerInterface $container
     * @return \Magomogo\Persisted\PropertyBag
     */
    public function propertiesFrom($container);
}
