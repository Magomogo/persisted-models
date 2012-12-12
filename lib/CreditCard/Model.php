<?php
namespace CreditCard;
use Model\PropertyContainer\ContainerInterface;
use Model\ContainerReadyInterface;

class Model implements ContainerReadyInterface
{
    /**
     * @var Properties
     */
    private $properties;

    public static function loadFrom(ContainerInterface $container, $id)
    {
        return new self($container->loadProperties(new Properties($id)));
    }

    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    public function maskedPan()
    {
        return substr($this->properties->pan, 0, 4) . ' **** **** ' . substr($this->properties->pan, 12, 4);
    }

    public function paymentSystem()
    {
        return $this->properties->system;
    }

    public function payFor($something)
    {

    }

    public function putIn(ContainerInterface $container)
    {
        return $container->saveProperties($this->properties)->id;
    }

    public function propertiesFrom(ContainerInterface $container)
    {
        return $this->properties->assertOriginIs($container);
    }
}
