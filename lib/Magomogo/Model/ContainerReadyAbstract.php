<?php
namespace Magomogo\Model;
use Magomogo\Model\PropertyContainer\ContainerInterface;

abstract class ContainerReadyAbstract implements ContainerReadyInterface
{
    /**
     * @var PropertyBag
     */
    protected $properties;

    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @return string unique identifier
     */
    public function putIn(ContainerInterface $container)
    {
        return $container->saveProperties($this->properties)->id;
    }

    /**
     * Confirms that properties has correct origin
     *
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @return \Magomogo\Model\PropertyBag
     */
    public function propertiesFrom(ContainerInterface $container)
    {
        return $this->properties->assertOriginIs($container);
    }
}
