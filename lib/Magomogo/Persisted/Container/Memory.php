<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\ModelInterface;
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

        return $propertyBag;
    }

    /**
     * @param CollectionBag $collectionBag
     * @param PropertyBag $ownerProperties
     * @param $connections
     * @internal param array $connections
     */
    public function referToMany($collectionBag, $ownerProperties, array $connections)
    {
        $this->saveProperties($ownerProperties);
        $this->manyToManyReferences[$collectionBag] = array();
        foreach ($connections as $rightProperties) {
            $this->manyToManyReferences[$collectionBag][] = array(
                'left' => $ownerProperties->id($this),
                'right' => $rightProperties,
            );
            $this->saveProperties($rightProperties);
        }
    }

    /**
     * @param string $collectionBag
     * @param PropertyBag $ownerProperties
     * @return array
     */
    public function listReferences($collectionBag, $ownerProperties)
    {
        $connections = array();
        foreach ($this->manyToManyReferences[$collectionBag] as $pair) {
            if ($ownerProperties->id($this) === $pair['left']) {
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
        $id = $propertyBag->id($this) ? : self::$autoincrement++;
        $propertyBag->persisted($id, $this);
        return $propertyBag;
    }
}
