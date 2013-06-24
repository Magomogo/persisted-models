<?php
namespace Test\CreditCard;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\PropertyBag;

class Model implements ModelInterface
{
    /**
     * @var Properties
     */
    private $properties;

    /**
     * @param string $id
     * @return PropertyBag
     */
    public static function newPropertyBag($id = null)
    {
        return new Properties($id);
    }

    /**
     * @param ContainerInterface $container
     * @return PropertyBag
     */
    public function propertiesFor($container)
    {
        return $this->properties;
    }

//----------------------------------------------------------------------------------------------------------------------

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
