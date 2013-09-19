<?php
namespace Magomogo\Persisted\Container;
use Magomogo\Persisted\PropertyBag;

interface ContainerInterface
{
    /**
     * @param \Magomogo\Persisted\PropertyBag $propertyBag
     * @return \Magomogo\Persisted\PropertyBag $propertyBag loaded with data
     */
    public function loadProperties($propertyBag);

    /**
     * @param \Magomogo\Persisted\PropertyBag $propertyBag
     * @return \Magomogo\Persisted\PropertyBag
     */
    public function saveProperties($propertyBag);

    /**
     * @param \Magomogo\Persisted\PropertyBag $propertyBag
     * @return void
     */
    public function deleteProperties($propertyBag);

    /**
     * @param string $referenceName
     * @param \Magomogo\Persisted\PropertyBag $leftProperties
     * @param array $connections array of \Magomogo\Model\PropertyBag
     * @return void
     */
    public function referToMany($referenceName, $leftProperties, array $connections);

    /**
     * @param string $referenceName
     * @param \Magomogo\Persisted\PropertyBag $leftProperties
     * @return array of \Magomogo\Model\PropertyBag
     */
    public function listReferences($referenceName, $leftProperties);
}
