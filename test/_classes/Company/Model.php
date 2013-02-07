<?php
namespace Company;
use Magomogo\Model\ContainerReadyAbstract;

class Model extends ContainerReadyAbstract
{
    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    public function name()
    {
        return $this->properties->name;
    }
}
