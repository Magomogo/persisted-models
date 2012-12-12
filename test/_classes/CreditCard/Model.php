<?php
namespace CreditCard;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Magomogo\Model\ContainerReadyInterface;

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
}
