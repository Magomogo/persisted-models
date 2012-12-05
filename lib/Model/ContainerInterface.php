<?php
namespace Model;
use Model\DataType\DataTypeInterface;

interface ContainerInterface
{
    /**
     * @param string $uniqueKey
     * @return self
     */
    public function begin($uniqueKey = null);

    /**
     * @param $name
     * @param DataTypeInterface $property
     * @return self
     */
    public function loadProperty($name, DataTypeInterface $property);

    /**
     * @param $name
     * @param DataTypeInterface $property
     * @return self
     */
    public function saveProperty($name, DataTypeInterface $property);

    /**
     * @return string unique key
     */
    public function commit();
}
