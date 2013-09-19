<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\Collection;
use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\AbstractProperties;
use Magomogo\Persisted\Exception\NotFound;

/**
 * This container can keep one model and all its references in memory.
 */
class Memory implements ContainerInterface
{
    private static $autoincrement = 1;

    /**
     * @var array of AbstractProperties
     */
    protected $storage = array();

    /**
     * @var array
     */
    protected $manyToManyReferences = array();

    /**
     * @param ModelInterface $model
     * @return AbstractProperties
     */
    public function exposeProperties($model)
    {
        $id = $model->save($this);
        return $this->storage[$id];
    }

    /**
     * @param AbstractProperties $propertyBag
     * @return AbstractProperties
     * @throws \Magomogo\Persisted\Exception\NotFound
     */
    public function loadProperties($propertyBag)
    {
        if (!array_key_exists($propertyBag->id($this), $this->storage)
        ) {
            throw new NotFound;
        }

        /** @var $properties AbstractProperties */
        $properties = $this->storage[$propertyBag->id($this)];
        $properties->copyTo($propertyBag);

        if ($propertyBag instanceof Collection\OwnerInterface) {
            $this->loadCollections($propertyBag);
        }

        return $propertyBag;
    }

    /**
     * @param AbstractProperties $propertyBag
     * @return AbstractProperties
     */
    public function saveProperties($propertyBag)
    {
        $this->notifyOnPersistence($propertyBag);
        $this->storage[$propertyBag->id($this)] = $propertyBag;

        foreach ($this->storage[$propertyBag->id($this)] as $value) {
            if ($value instanceof ModelInterface) {
                $value->save($this);
            }
        }

        if ($propertyBag instanceof Collection\OwnerInterface) {
            $this->saveCollections($propertyBag);
        }

        return $propertyBag;
    }

    /**
     * @param Collection\AbstractCollection $collectionBag
     * @param Collection\OwnerInterface $leftProperties
     * @param array $propertyBags
     */
    public function referToMany($collectionBag, $leftProperties, array $propertyBags)
    {
        $refName = $this->manyToManyRefName($collectionBag, $leftProperties);

        $this->manyToManyReferences[$refName] = array();
        foreach ($propertyBags as $rightProperties) {
            $this->manyToManyReferences[$refName][] = array(
                'left' => $leftProperties->id($this),
                'right' => $rightProperties,
            );
            $this->saveProperties($rightProperties);
        }
    }

    /**
     * @param string $collectionBag
     * @param AbstractProperties $leftProperties
     * @return array
     */
    public function listReferences($collectionBag, $leftProperties)
    {
        $refName = $this->manyToManyRefName($collectionBag, $leftProperties);
        $connections = array();
        foreach ($this->manyToManyReferences[$refName] as $pair) {
            if ($leftProperties->id($this) === $pair['left']) {
                $connections[] = $pair['right'];
            }
        }
        return $connections;
    }

    /**
     * @param array $propertyBags array of \Magomogo\Model\AbstractProperties
     * @return void
     */
    public function deleteProperties(array $propertyBags)
    {
        $this->storage = array();
        $this->manyToManyReferences = array();
    }

    /**
     * @param AbstractProperties $propertyBag
     * @return AbstractProperties
     */
    private function notifyOnPersistence($propertyBag)
    {
        $id = $propertyBag->id($this) ?: $propertyBag->naturalKey() ?: self::$autoincrement++;
        $propertyBag->persisted($id, $this);
        return $propertyBag;
    }

    /**
     * @param Collection\OwnerInterface $propertyBag
     */
    private function loadCollections($propertyBag)
    {
        /** @var Collection\AbstractCollection $collection */
        foreach ($propertyBag->collections() as $collection) {
            $collection->loadFrom($this, $propertyBag);
        }
    }

    /**
     * @param Collection\OwnerInterface $propertyBag
     */
    private function saveCollections($propertyBag)
    {
        /** @var Collection\AbstractCollection $collection */
        foreach ($propertyBag->collections() as $collection) {
            $collection->putIn($this, $propertyBag);
        }
    }

    private function manyToManyRefName($collection, $owner)
    {
        return get_class($collection) . '-' . get_class($owner);
    }
}
