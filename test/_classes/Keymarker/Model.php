<?php
namespace Keymarker;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Magomogo\Model\ContainerReadyAbstract;

class Model extends ContainerReadyAbstract
{
    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return \Keymarker\Model
     */
    public static function loadFrom($container, $id)
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
