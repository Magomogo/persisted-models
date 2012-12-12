<?php
namespace Keymarker;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Magomogo\Model\ContainerReadyInterface;

class Model implements ContainerReadyInterface
{
    /**
     * @var Properties
     */
    private $props;

    public static function loadFrom(ContainerInterface $container, $id)
    {
        $props = new Properties($id);
        $container->loadProperties($props);
        return new self($props);
    }

    public function __construct(Properties $props)
    {
        $this->props = $props;
    }

    public function __toString()
    {
        return $this->props->id;
    }

    public function putIn(ContainerInterface $container)
    {
        return $container->saveProperties($this->props)->id;
    }

    public function propertiesFrom(ContainerInterface $container)
    {
        return $this->props->assertOriginIs($container);
    }
}
