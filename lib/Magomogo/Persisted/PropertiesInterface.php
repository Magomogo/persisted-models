<?php
namespace Magomogo\Persisted;

use Magomogo\Persisted\Container\ContainerInterface;

interface PropertiesInterface
{
    /**
     * Returns unique identifier in given container
     *
     * @param ContainerInterface $container
     * @return mixed
     */
    public function id($container);

    /**
     * Optionally defines natural key value
     * null value forces container to set its artificial identifier
     *
     * @return mixed
     */
    public function naturalKey();

    /**
     * The message. A container stored properties giving particular identifier
     *
     * @param mixed $id
     * @param ContainerInterface $container
     */
    public function persisted($id, $container);

    /**
     * Load all values from the container
     *
     * @param ContainerInterface$container
     * @param mixed $id
     * @return $this
     */
    public function loadFrom($container, $id);

    /**
     * Save all value to the container
     *
     * @param ContainerInterface $container
     * @return string unique identifier
     */
    public function putIn($container);

    /**
     * Delete this properties form the container
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function deleteFrom($container);

    /**
     * Copy state to another properties of same type
     *
     * @param self $properties
     * @return self
     */
    public function copyTo($properties);
}
