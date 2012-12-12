<?php
namespace Company;
use Magomogo\Model\ContainerReadyInterface;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Employee;
use Person;

class Model implements ContainerReadyInterface
{
    use \Magomogo\Model\ContainerUtils;

    public static function loadFrom(ContainerInterface $container, $id)
    {
        return new self($container->loadProperties(new Properties($id)));
    }

    public function __construct(Properties $props)
    {
        $this->properties = $props;
    }

    public function name()
    {
        return $this->properties->name;
    }
}
