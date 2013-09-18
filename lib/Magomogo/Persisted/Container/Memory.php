<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\CollectionOwnerInterface;
use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\PropertyBag;
use Magomogo\Persisted\Exception\NotFound;
use Magomogo\Persisted\PropertyBagCollection;

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
     * @param ModelInterface $model
     * @return PropertyBag
     */
    public function exposeProperties($model)
    {
        $id = $model->save($this);
        return $this->storage[$id];
    }

    /**
     * @param PropertyBag $propertyBag
     * @return PropertyBag
     * @throws \Magomogo\Persisted\Exception\NotFound
     */
    public function loadProperties($propertyBag)
    {
        if (!array_key_exists($propertyBag->id($this), $this->storage)
        ) {
            throw new NotFound;
        }

        /** @var $properties PropertyBag */
        $properties = $this->storage[$propertyBag->id($this)];
        $properties->copyTo($propertyBag);

        if ($propertyBag instanceof CollectionOwnerInterface) {
            $this->loadCollections($propertyBag);
        }

        return $propertyBag;
    }

    /**
     * @param PropertyBag $propertyBag
     * @return PropertyBag
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

        if ($propertyBag instanceof CollectionOwnerInterface) {
            $this->saveCollections($propertyBag);
        }

        return $propertyBag;
    }

    /**
     * @param PropertyBagCollection $collectionBag
     * @param CollectionOwnerInterface $leftProperties
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
     * @param PropertyBag $leftProperties
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
     * @param array $propertyBags array of \Magomogo\Model\PropertyBag
     * @return void
     */
    public function deleteProperties(array $propertyBags)
    {
        $this->storage = array();
        $this->manyToManyReferences = array();
    }

    /**
     * @param PropertyBag $propertyBag
     * @return PropertyBag
     */
    private function notifyOnPersistence($propertyBag)
    {
        $id = $propertyBag->id($this) ?: $propertyBag->naturalKey() ?: self::$autoincrement++;
        $propertyBag->persisted($id, $this);
        return $propertyBag;
    }

    /**
     * @param CollectionOwnerInterface $propertyBag
     */
    private function loadCollections($propertyBag)
    {
        /** @var PropertyBagCollection $collection */
        foreach ($propertyBag->collections() as $collection) {
            $collection->loadFrom($this, $propertyBag);
        }
    }

    /**
     * @param CollectionOwnerInterface $propertyBag
     */
    private function saveCollections($propertyBag)
    {
        /** @var PropertyBagCollection $collection */
        foreach ($propertyBag->collections() as $collection) {
            $collection->putIn($this, $propertyBag);
        }
    }

    private function manyToManyRefName($collection, $owner)
    {
        return get_class($collection) . '-' . get_class($owner);
    }
}
