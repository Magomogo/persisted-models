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
     * @param string $referenceName
     * @param PropertyBag $leftProperties
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
     * @param PropertyBag $leftProperties
     * @return array
     */
    public function listReferences($referenceName, $leftProperties)
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
     * @param \Magomogo\Persisted\PropertyBag $propertyBag
     * @return void
     */
    public function deleteProperties($propertyBag)
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
