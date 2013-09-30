<?php

namespace Magomogo\Persisted\Container;

use Doctrine\CouchDB\CouchDBClient;
use Magomogo\Persisted\AbstractProperties;
use Magomogo\Persisted\Collection;
use Magomogo\Persisted\Exception;
use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\PossessionInterface;

class CouchDb implements ContainerInterface
{
    /**
     * @var CouchDBClient
     */
    private $client;

    /**
     * @param CouchDBClient $client
     */
    function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param AbstractProperties $properties
     * @return AbstractProperties loaded with data
     */
    public function loadProperties($properties)
    {
        $doc = $this->loadDocument($properties->id($this));

        if (!is_null($properties->naturalKeyFieldName())) {
            $doc[$properties->naturalKeyFieldName()] = $doc['_id'];
        }

        foreach ($properties as $name => &$property) {
            $property = array_key_exists($name, $doc) ? $this->fromDbValue($property, $doc[$name]) : null;
        }

        if ($properties instanceof PossessionInterface) {
            $this->collectReferences($doc, $properties->foreign());
        }

        if ($properties instanceof Collection\OwnerInterface) {
            $this->loadCollections($properties->collections(), $properties);
        }

        return $properties;
    }

    /**
     * @param AbstractProperties $properties
     * @return AbstractProperties
     */
    public function saveProperties($properties)
    {
        $doc = array('type' => get_class($properties));

        foreach ($properties as $name => $value) {
            $doc[$name] = $this->toDbValue($value);
        }

        if ($properties instanceof PossessionInterface) {
            $doc = array_merge($doc, $this->foreignKeys($properties->foreign()));
        }

        if (!is_null($properties->naturalKeyFieldName())) {
            $doc = self::applyNaturalKey($doc, $properties);
        }

        if ($properties->id($this) && is_array($existingDoc = $this->loadDocument($properties->id($this)))) {
            $doc = array_merge_recursive($doc, $existingDoc);
            $this->client->putDocument($doc, $properties->id($this));
        } else {
            list($id, $rev) = $this->client->postDocument($doc);
            $properties->persisted($id, $this);
        }

        if ($properties instanceof Collection\OwnerInterface) {
            $this->saveCollections($properties->collections(), $properties);
        }

        return $properties;
    }

    /**
     * @param AbstractProperties $properties
     * @return void
     */
    public function deleteProperties($properties)
    {
        if (is_array($existingDoc = $this->loadDocument($properties->id($this)))) {
            $this->client->deleteDocument($existingDoc['_id'], $existingDoc['_rev']);
        }
    }

//----------------------------------------------------------------------------------------------------------------------

    /**
     * @param $id
     * @throws \Magomogo\Persisted\Exception\NotFound
     * @return array
     */
    private function loadDocument($id)
    {
        $response = $this->client->findDocument($id);
        if (200 === $response->status) {
            return $response->body;
        }
        throw new Exception\NotFound;
    }

    private static function dateInIso8601($str)
    {
        return new \DateTime(date('c', strtotime($str)));
    }

    private function fromDbValue($property, $value)
    {
        if ($property instanceof ModelInterface) {
            return is_null($value) ? null : $property::load($this, $value);
        } elseif($property instanceof \DateTime) {
            return self::dateInIso8601($value);
        }
        return $value;
    }

    private function toDbValue($property)
    {
        if (is_scalar($property) || is_null($property)) {
            return $property;
        } elseif ($property instanceof ModelInterface) {
            return $property->save($this);
        } elseif ($property instanceof \DateTime) {
            return $property->format('c');
        } else {
            throw new Exception\Type;
        }
    }

    private function foreignKeys($references)
    {
        $keys = array();
        /* @var AbstractProperties $properties */
        foreach ($references as $referenceName => $properties) {
            $keys[$referenceName] = $properties->id($this);
        }

        return $keys;
    }

    private function collectReferences(array $doc, $references)
    {
        /* @var AbstractProperties $properties */
        foreach ($references as $referenceName => $properties) {
            $properties->loadFrom($this, $doc[$referenceName]);
        }
        return $references;
    }

    /**
     * @param $collections array of Collection\AbstractCollection
     * @param Collection\OwnerInterface $ownerProperties
     */
    private function saveCollections($collections, $ownerProperties)
    {
        /** @var Collection\AbstractCollection $collection */
        foreach ($collections as $name => $collection) {
            $couchDbClient = $this->client;
            $doc = $this->loadDocument($ownerProperties->id($this));
            $container = $this;

            $collection->propertiesOperation(function($items) use ($name, $couchDbClient, $doc, $container) {
                $doc[$name] = array();

                foreach ($items as $rightProperties) {
                    $doc[$name][] = $rightProperties->id($container);
                }

                $couchDbClient->putDocument($doc, $doc['_id']);
                return $items;
            });
        }
    }

    /**
     * @param $collections array of Collection\AbstractCollection
     * @param Collection\OwnerInterface $ownerProperties
     */
    private function loadCollections($collections, $ownerProperties)
    {
        $doc = $this->loadDocument($ownerProperties->id($this));
        $container = $this;

        /** @var Collection\AbstractCollection $collection */
        foreach ($collections as $name => $collection) {
            $propertiesIds = $doc[$name];

            $collection->propertiesOperation(function() use ($propertiesIds, $collection, $container) {
                $items = array();
                foreach ($propertiesIds as $id) {
                    $rightProperties = $collection->constructProperties();
                    $items[] = $rightProperties->loadFrom($container, $id);
                }
                return $items;
           });
        }
    }

    /**
     * @param array $doc
     * @param AbstractProperties $properties
     * @return array
     */
    private static function applyNaturalKey($doc, $properties)
    {
        $doc['_id'] = $properties->{$properties->naturalKeyFieldName()};
        unset($doc[$properties->naturalKeyFieldName()]);
        return $doc;
    }

}