<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\AbstractProperties;

interface ContainerInterface
{
    /**
     * @param AbstractProperties $properties
     * @return AbstractProperties loaded with data
     */
    public function loadProperties($properties);

    /**
     * @param AbstractProperties $properties
     * @return AbstractProperties
     */
    public function saveProperties($properties);

    /**
     * @param AbstractProperties $properties
     * @return void
     */
    public function deleteProperties($properties);
}
