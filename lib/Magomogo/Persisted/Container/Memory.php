<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\PropertyBag;
use Magomogo\Persisted\Exception\NotFound;

/**
 * This container can keep one model and all its references in memory.
 */
class Memory implements ContainerInterface
{
    /**
     * @var array of PropertyBag
     */
    protected $storage = array();

    /**
     * @var array
     */
    protected $manyToManyReferences = array();

    /**
     * @param \Magomogo\Persisted\PropertyBag $propertyBag
     * @return \Magomogo\Persisted\PropertyBag
     * @throws \Magomogo\Persisted\Exception\NotFound
     */
    public function loadProperties($propertyBag)
    {
        if (!array_key_exists(get_class($propertyBag), $this->storage)) {
            throw new NotFound;
        }

        /** @var $properties PropertyBag */
        $properties = $this->storage[get_class($propertyBag)][$propertyBag->id($this)];

        foreach ($properties as $name => $property) {
            $propertyBag->$name = $property;
        }

        foreach($properties->foreign() as $referenceName => $referenceProperties) {
            foreach ($referenceProperties as $name => $property) {
                $propertyBag->foreign()->$referenceName->$name = $property;
            }
        }

        return $propertyBag;
    }

    /**
     * @param \Magomogo\Persisted\PropertyBag $propertyBag
     * @return \Magomogo\Persisted\PropertyBag
     */
    public function saveProperties($propertyBag)
    {
        $this->storage[get_class($propertyBag)][$propertyBag->id($this)] = $propertyBag;
        foreach ($propertyBag->foreign() as $referenceProperties) {
            $this->saveProperties($referenceProperties);
        }
        return $propertyBag;
    }

    /**
     * @param string $referenceName
     * @param \Magomogo\Persisted\PropertyBag $leftProperties
     * @param array $connections
     */
    public function referToMany($referenceName, $leftProperties, array $connections)
    {
        $this->manyToManyReferences[$referenceName] = array();
        foreach ($connections as $rightProperties) {
            $this->manyToManyReferences[$referenceName][] = array(
                'left' => $leftProperties,
                'right' => $rightProperties,
            );
            $this->saveProperties($rightProperties);
        }
        $this->saveProperties($leftProperties);
    }

    /**
     * @param string $referenceName
     * @param \Magomogo\Persisted\PropertyBag $leftProperties
     * @param string $rightPropertiesSample
     * @return array
     */
    public function listReferences($referenceName, $leftProperties, $rightPropertiesSample)
    {
        $connections = array();
        foreach ($this->manyToManyReferences[$referenceName] as $pair) {
            if ($leftProperties === $pair['left']) {
                $connections[] = $pair['right'];
            }
        }

        return $connections;
    }

    /**
     * @param array $propertyBags array of \Magomogo\Model\PropertyBag
     * @return void
     */
    public function deleteProperties(array $propertyBags)
    {
        $this->storage = array();
        $this->manyToManyReferences = array();
    }
}
