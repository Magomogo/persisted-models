<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\Collection;
use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\AbstractProperties;
use Magomogo\Persisted\Exception\NotFound;
use Magomogo\Persisted\PossessionInterface;

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
        $this->copyProperties($properties, $targetProperties);

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
            $references = $this->manyToManyReferences[$this->manyToManyRefName($collection, $properties)];
            $ownId = $properties->id($this);

            $collection->propertiesOperation(
                function() use ($references, $ownId) {
                    $items = array();
                    foreach ($references as $pair) {
                        if ($ownId === $pair['left']) {
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
            $ownId = $properties->id($this);
            $container = $this;

            $collection->propertiesOperation(
                function($items) use (&$referenceStorage, $ownId, $container) {
                    foreach ($items as $rightProperties) {
                        $referenceStorage[] = array(
                            'left' => $ownId,
                            'right' => $rightProperties,
                        );
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


    /**
     * @param AbstractProperties $source
     * @param AbstractProperties $destination
     */
    private function copyProperties($source, $destination)
    {
        foreach ($source as $name => $property) {
            $destination->$name = $property;
        }

        if (($source instanceof PossessionInterface) && ($destination instanceof PossessionInterface)) {
            foreach($source->foreign() as $referenceName => $referenceProperties) {
                $this->copyProperties($referenceProperties, $destination->foreign()->$referenceName);
                if ($referenceProperties->id($this)) {
                    $destination->foreign()->$referenceName->persisted($referenceProperties->id($this), $this);
                }
            }
        }

    }

}
