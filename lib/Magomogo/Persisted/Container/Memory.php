<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\PropertyBag;
use Magomogo\Persisted\Exception\NotFound;

/**
 * This container can keep one model and all its references in memory.
 */
class Memory implements ContainerInterface
{
    private static $autoincrement = 1;

    /**
     * @var array of PropertyBag
     */
    protected $storage = array();

    /**
     * @var array
     */
    protected $manyToManyReferences = array();

    /**
     * @param string $type
     * @param string $id
     * @return null|PropertyBag
     */
    public function query($type, $id)
    {
        if (!array_key_exists($type, $this->storage)
            || !array_key_exists($id, $this->storage[$type])
        ) {
            return null;
        }
        return $this->storage[$type][$id];
    }

    /**
     * @param \Magomogo\Persisted\PropertyBag $propertyBag
     * @return \Magomogo\Persisted\PropertyBag
     * @throws \Magomogo\Persisted\Exception\NotFound
     */
    public function loadProperties($propertyBag)
    {
        if (!array_key_exists(get_class($propertyBag), $this->storage)
            || !array_key_exists($propertyBag->id($this), $this->storage[get_class($propertyBag)])
        ) {
            throw new NotFound;
        }

        /** @var $properties PropertyBag */
        $properties = $this->storage[get_class($propertyBag)][$propertyBag->id($this)];
        $properties->copyTo($propertyBag);
        return $propertyBag;
    }

    /**
     * @param \Magomogo\Persisted\PropertyBag $propertyBag
     * @return \Magomogo\Persisted\PropertyBag
     */
    public function saveProperties($propertyBag)
    {
        $id = $propertyBag->id($this) ?: self::$autoincrement++;
        $propertyBag->persisted($id, $this);
        $this->storage[get_class($propertyBag)][$propertyBag->id($this)] = clone $propertyBag;
        return $propertyBag;
    }

    /**
     * @param string $referenceName
     * @param \Magomogo\Persisted\PropertyBag $leftProperties
     * @param array $connections
     */
    public function referToMany($referenceName, $leftProperties, array $connections)
    {
        $this->saveProperties($leftProperties);
        $this->manyToManyReferences[$referenceName] = array();
        foreach ($connections as $rightProperties) {
            $this->manyToManyReferences[$referenceName][] = array(
                'left' => $leftProperties->id($this),
                'right' => $rightProperties,
            );
            $this->saveProperties($rightProperties);
        }
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
            if ($leftProperties->id($this) === $pair['left']) {
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
