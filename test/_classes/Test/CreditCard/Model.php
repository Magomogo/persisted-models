<?php
namespace Test\CreditCard;

use Magomogo\Persisted\PersistedAbstract;

class Model extends PersistedAbstract
{
    /**
     * @param \Magomogo\Persisted\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom($container, $id)
    {
        return new self($container->loadProperties(new Properties($id)));
    }

    public function __construct($properties)
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
}
