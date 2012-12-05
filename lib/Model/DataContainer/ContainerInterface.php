<?php
namespace Model\DataContainer;

interface ContainerInterface
{
    /**
     * @param array $properties name to Model\DataType\DataTypeInterface map
     * @return self
     */
    public function loadProperties(array $properties);

    /**
     * @param array $properties name to Model\DataType\DataTypeInterface map
     * @return array unique key
     */
    public function saveProperties(array $properties);
}
