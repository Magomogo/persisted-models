<?php
namespace Magomogo\Persisted;
use Magomogo\Persisted\Container\ContainerInterface;

interface PersistedInterface
{
    /**
     * @param \Magomogo\Persisted\Container\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom($container, $id);

    /**
     * @param \Magomogo\Persisted\Container\ContainerInterface $container
     * @return string unique identifier
     */
    public function putIn($container);

    /**
     * @param Container\ContainerInterface $container
     * @return void
     */
    public function deleteFrom($container);

    /**
     * Confirms that properties has correct origin
     *
     * @param \Magomogo\Persisted\Container\ContainerInterface $container
     * @return \Magomogo\Persisted\PropertyBag
     */
    public function propertiesFrom($container);
}
