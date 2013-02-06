<?php
namespace Magomogo\Model\PropertyContainer;

use Magomogo\Model\PropertyBag;
use Magomogo\Model\Exception\NotFound;

/**
 * This container can keep one model and all its references in memory.
 */
class Memory implements ContainerInterface
{
    /**
     * @var PropertyBag
     */
    protected $properties;

    /**
     * @var array
     */
    protected $references = array();

    /**
     * @var array
     */
    protected $manyToManyReferences = array();

    /**
     * @param \Magomogo\Model\PropertyBag $propertyBag
     * @param array $references
     * @return \Magomogo\Model\PropertyBag
     * @throws \Magomogo\Model\Exception\NotFound
     */
    public function loadProperties($propertyBag, array $references = array())
    {
        if (is_null($this->properties)) {
            throw new NotFound;
        }

        foreach ($this->properties as $name => $property) {
            $propertyBag->$name = $property;
        }

        /* @var PropertyBag $storedProperties */
        foreach ($this->references as $referenceName => $storedProperties) {
            foreach ($references[$referenceName] as $name => $property) {
                $references[$referenceName]->$name = $storedProperties->$name;
            }
        }
        return $propertyBag;
    }

    /**
     * @param \Magomogo\Model\PropertyBag $propertyBag
     * @param array $references
     * @return \Magomogo\Model\PropertyBag
     */
    public function saveProperties($propertyBag, array $references = array())
    {
        $this->properties = $propertyBag;
        $this->references = $references;
        return $propertyBag;
    }

    /**
     * @param string $referenceName
     * @param \Magomogo\Model\PropertyBag $leftProperties
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
        }
    }

    /**
     * @param string $referenceName
     * @param \Magomogo\Model\PropertyBag $leftProperties
     * @param string $rightPropertiesClassName
     * @return array
     */
    public function listReferences($referenceName, $leftProperties, $rightPropertiesClassName)
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
        $this->properties = null;
        $this->references = array();
        $this->manyToManyReferences = array();
    }
}
