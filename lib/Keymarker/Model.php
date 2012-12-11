<?php
namespace Keymarker;
use Model\PropertyContainer\ContainerInterface;
use Model\ContainerReadyInterface;

class Model implements ContainerReadyInterface
{
    use \Model\ContainerUtils;

    public static function loadFrom(ContainerInterface $container, $id)
    {
        return new self($container->loadProperties(new Properties($id)));
    }

    public function __construct(Properties $props)
    {
        $this->properties = $props;
    }

    public function __toString()
    {
        return $this->properties->title;
    }
}
