<?php
namespace Keymarker;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Magomogo\Model\ContainerReadyAbstract;

class Model extends ContainerReadyAbstract
{
    public static function loadFrom(ContainerInterface $container, $id)
    {
        $props = new Properties($id);
        $container->loadProperties($props);
        return new self($props);
    }

    public function __construct(Properties $props)
    {
        $this->properties = $props;
    }

    public function __toString()
    {
        return $this->properties->id;
    }
}
