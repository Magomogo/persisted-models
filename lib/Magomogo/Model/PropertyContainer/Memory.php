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
    private $properties;

    /**
     * @var array
     */
    private $references = array();

    /**
     * @var array
     */
    private $manyToManyReferences = array();

    public function loadProperties(PropertyBag $propertyBag, array $references = array())
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
    public function saveProperties(PropertyBag $propertyBag, array $references = array())
    {
        $this->properties = $propertyBag;
        $this->references = $references;
        return $propertyBag;
    }

    public function referToMany($referenceName, PropertyBag $leftProperties, array $connections)
    {
        $this->manyToManyReferences[$referenceName] = array();
        foreach ($connections as $rightProperties) {
            $this->manyToManyReferences[$referenceName][] = array(
                'left' => $leftProperties,
                'right' => $rightProperties,
            );
        }
    }

    public function listReferences($referenceName, PropertyBag $leftProperties, $rightPropertiesClassName)
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
    }
}
