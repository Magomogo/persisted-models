<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\Collection;
use Magomogo\Persisted\AbstractProperties;

interface ContainerInterface
{
    /**
     * @param \Magomogo\Persisted\AbstractProperties $properties
     * @return \Magomogo\Persisted\AbstractProperties loaded with data
     */
    public function loadProperties($properties);

    /**
     * @param \Magomogo\Persisted\AbstractProperties $properties
     * @return \Magomogo\Persisted\AbstractProperties
     */
    public function saveProperties($properties);

    /**
     * @param \Magomogo\Persisted\AbstractProperties $properties
     * @return void
     */
    public function deleteProperties($properties);

    /**
     * @param Collection\AbstractCollection $collection
     * @param Collection\OwnerInterface $leftProperties
     * @param array $manyProperties array of \Magomogo\Model\AbstractProperties
     * @return void
     */
    public function referToMany($collection, $leftProperties, array $manyProperties);

    /**
     * @param Collection\AbstractCollection $collection
     * @param Collection\OwnerInterface $leftProperties
     * @return array of \Magomogo\Model\AbstractProperties
     */
    public function listReferences($collection, $leftProperties);
}
