<?php

namespace Magomogo\Persisted\Container;

use Doctrine\CouchDB\CouchDBClient;
use Magomogo\Persisted\AbstractProperties;
use Magomogo\Persisted\Collection;
use Magomogo\Persisted\Exception;
use Magomogo\Persisted\ModelInterface;

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
     * @param \Magomogo\Persisted\AbstractProperties $properties
     * @return \Magomogo\Persisted\AbstractProperties loaded with data
     */
    public function loadProperties($properties)
    {
        $doc = $this->loadDocument($properties->id($this));

        foreach ($properties as $name => &$property) {
            $property = array_key_exists($name, $doc) ? self::fromDbValue($property, $doc[$name]) : null;
        }

        return $properties;
    }

    /**
     * @param \Magomogo\Persisted\AbstractProperties $properties
     * @return \Magomogo\Persisted\AbstractProperties
     */
    public function saveProperties($properties)
    {
        $doc = array();

        foreach ($properties as $name => $value) {
            $doc[$name] = self::toDbValue($value);
        }

        if ($properties->id($this) && is_array($existingDoc = $this->loadDocument($properties->id($this)))) {
            $doc = array_merge_recursive($doc, $existingDoc);
            $this->client->putDocument($doc, $properties->id($this));
        } else {
            list($id, $rev) = $this->client->postDocument($doc);
            $properties->persisted($id, $this);
        }
        return $properties;
    }

    /**
     * @param \Magomogo\Persisted\AbstractProperties $properties
     * @return void
     */
    public function deleteProperties($properties)
    {
        if (is_array($existingDoc = $this->loadDocument($properties->id($this)))) {
            $this->client->deleteDocument($existingDoc['_id'], $existingDoc['_rev']);
        }
    }

    /**
     * @param Collection\AbstractCollection $collection
     * @param Collection\OwnerInterface $leftProperties
     * @param array $manyProperties array of \Magomogo\Model\AbstractProperties
     * @return void
     */
    public function referToMany($collection, $leftProperties, array $manyProperties)
    {
        // TODO: Implement referToMany() method.
    }

    /**
     * @param Collection\AbstractCollection $collection
     * @param Collection\OwnerInterface $leftProperties
     * @return array of \Magomogo\Model\AbstractProperties
     */
    public function listReferences($collection, $leftProperties)
    {
        // TODO: Implement listReferences() method.
        return array();
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

}