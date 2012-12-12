<?php
namespace Magomogo\Model;
use Magomogo\Model\PropertyContainer\ContainerInterface;

interface ContainerReadyInterface
{
    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom(ContainerInterface $container, $id);

    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @return string unique identifier
     */
    public function putIn(ContainerInterface $container);

    /**
     * Confirms that properties has correct origin
     *
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @return \Magomogo\Model\PropertyBag
     */
    public function propertiesFrom(ContainerInterface $container);
}

trait ContainerUtils
{
    /**
     * @var PropertyBag
     */
    protected $properties;

    public function putIn(ContainerInterface $container)
    {
        return $container->saveProperties($this->properties)->id;
    }

    public function propertiesFrom(ContainerInterface $container)
    {
        return $this->properties->assertOriginIs($container);
    }
}