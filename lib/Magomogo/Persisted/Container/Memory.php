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
     * @var AbstractProperties[]
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
     * @param AbstractProperties $targetProperties
     * @return AbstractProperties
     * @throws NotFound
     */
    public function loadProperties($targetProperties)
    {
        if (!array_key_exists($targetProperties->id($this), $this->storage)
        ) {
            throw new NotFound;
        }

        /** @var $properties AbstractProperties */
        $properties = $this->storage[$targetProperties->id($this)];
        $properties->copyTo($targetProperties);

        if ($targetProperties instanceof Collection\OwnerInterface) {
            $this->loadCollections($targetProperties);
        }

        return $properties;
    }

    /**
     * @param AbstractProperties $properties
     * @return AbstractProperties
     */
    public function saveProperties($properties)
    {
        $this->notifyOnPersistence($properties);
        $this->storage[$properties->id($this)] = $properties;

        foreach ($this->storage[$properties->id($this)] as $value) {
            if ($value instanceof ModelInterface) {
                $value->save($this);
            }
        }

        if ($properties instanceof Collection\OwnerInterface) {
            $this->saveCollections($properties);
        }

        return $properties;
    }

    /**
     * @param AbstractProperties $properties
     * @return void
     */
    public function deleteProperties($properties)
    {
        $this->storage = array();
        $this->manyToManyReferences = array();
    }

    /**
     * @param AbstractProperties $properties
     * @return AbstractProperties
     */
    private function notifyOnPersistence($properties)
    {
        $id = $properties->id($this) ?: $properties->naturalKey() ?: self::$autoincrement++;
        $properties->persisted($id, $this);
        return $properties;
    }

    /**
     * @param Collection\OwnerInterface $properties
     */
    private function loadCollections($properties)
    {
        /** @var Collection\AbstractCollection $collection */
        foreach ($properties->collections() as $collection) {
            $references =  $this->manyToManyReferences[$this->manyToManyRefName($collection, $properties)];
            $id = $properties->id($this);

            $collection->propertiesOperation(
                function() use ($references, $properties, $id) {
                    $items = array();
                    foreach ($references as $pair) {
                        if ($id === $pair['left']) {
                            $items[] = $pair['right'];
                        }
                    }
                    return $items;
                }
            );
        }
    }

    /**
     * @param Collection\OwnerInterface $properties
     */
    private function saveCollections($properties)
    {
        /** @var Collection\AbstractCollection $collection */
        foreach ($properties->collections() as $collection) {
            $refName = $this->manyToManyRefName($collection, $properties);
            $this->manyToManyReferences[$refName] = array();
            $referenceStorage = &$this->manyToManyReferences[$refName];
            $id = $properties->id($this);
            $container = $this;

            $collection->propertiesOperation(
                function($items) use (&$referenceStorage, $id, $container) {
                    foreach ($items as $rightProperties) {
                        $referenceStorage[] = array(
                            'left' => $id,
                            'right' => $rightProperties,
                        );
                        $container->saveProperties($rightProperties);
                    }
                    return $items;
                }
            );
        }
    }

    private function manyToManyRefName($collection, $owner)
    {
        return get_class($collection) . '-' . get_class($owner);
    }
}
