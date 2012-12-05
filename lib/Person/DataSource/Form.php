<?php
namespace Person\DataSource;

class Form implements \Model\DataSourceInterface
{
    /**
     * @var array
     */
    private $nameToValueMap;

    /**
     * @param array $nameToValueMap;
     */
    public function __construct(array $nameToValueMap)
    {
        $this->nameToValueMap = $nameToValueMap;
    }

    public function readValue($propertyName)
    {
        return $this->nameToValueMap[$propertyName];
    }
}
