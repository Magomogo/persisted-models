<?php
namespace Magomogo\Persisted;
use Magomogo\Persisted\PropertyContainer\ContainerInterface;

abstract class ContainerReadyAbstract implements ContainerReadyInterface
{
    /**
     * @var PropertyBag
     */
    protected $properties;

    /**
     * @param \Magomogo\Persisted\PropertyContainer\ContainerInterface $container
     * @return string unique identifier
     */
    public function putIn($container)
    {
        return $container->saveProperties($this->properties)->id;
    }

    /**
     * @param \Magomogo\Persisted\PropertyContainer\ContainerInterface $container
     * @return void
     */
    public function deleteFrom($container)
    {
        $container->deleteProperties(array($this->properties));
    }

    /**
     * Confirms that properties has correct origin
     *
     * @param \Magomogo\Persisted\PropertyContainer\ContainerInterface $container
     * @return \Magomogo\Persisted\PropertyBag
     */
    public function propertiesFrom($container)
    {
        return $this->properties->assertOriginIs($container);
    }
}
