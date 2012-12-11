<?php
namespace Keymarker;
use Model\DataContainer\ContainerInterface;
use Model\ContainerReadyInterface;

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
        return $this->props->title;
    }

    public function putIn(ContainerInterface $container)
    {
        return $container->saveProperties($this->props)->id;
    }

    public function confirmOrigin(ContainerInterface $container)
    {
        return $this->props->confirmOrigin($container);
    }
}
