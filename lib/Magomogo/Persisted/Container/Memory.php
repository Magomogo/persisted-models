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
     * @param Collection\AbstractCollection $collection
     * @param Collection\OwnerInterface $leftProperties
     * @param AbstractProperties[] $manyProperties
     */
    public function referToMany($collection, $leftProperties, array $manyProperties)
    {
        $refName = $this->manyToManyRefName($collection, $leftProperties);

        $this->manyToManyReferences[$refName] = array();
        foreach ($manyProperties as $rightProperties) {
            $this->manyToManyReferences[$refName][] = array(
                'left' => $leftProperties->id($this),
                'right' => $rightProperties,
            );
            $this->saveProperties($rightProperties);
        }
    }

    /**
     * @param string $collection
     * @param AbstractProperties $leftProperties
     * @return AbstractProperties[]
     */
    public function listReferences($collection, $leftProperties)
    {
        $refName = $this->manyToManyRefName($collection, $leftProperties);
        $connections = array();
        foreach ($this->manyToManyReferences[$refName] as $pair) {
            if ($leftProperties->id($this) === $pair['left']) {
                $connections[] = $pair['right'];
            }
        }
        return $connections;
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
            $collection->loadFrom($this, $properties);
        }
    }

    /**
     * @param Collection\OwnerInterface $properties
     */
    private function saveCollections($properties)
    {
        /** @var Collection\AbstractCollection $collection */
        foreach ($properties->collections() as $collection) {
            $collection->putIn($this, $properties);
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
