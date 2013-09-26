<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\Collection;
use Magomogo\Persisted\AbstractProperties;

interface ContainerInterface
{
    /**
     * @param AbstractProperties $properties
     * @return AbstractProperties loaded with data
     */
    public function loadProperties($properties);

    /**
     * @param AbstractProperties $properties
     * @return AbstractProperties
     */
    public function saveProperties($properties);

    /**
     * @param AbstractProperties $properties
     * @return void
     */
    public function deleteProperties($properties);

    /**
     * @param Collection\AbstractCollection $collection
     * @param Collection\OwnerInterface $leftProperties
     * @param AbstractProperties[] $manyProperties
     * @return void
     */
    public function referToMany($collection, $leftProperties, array $manyProperties);

    /**
     * @param Collection\AbstractCollection $collection
     * @param Collection\OwnerInterface $leftProperties
     * @return AbstractProperties[]
     */
    public function listReferences($collection, $leftProperties);
}
