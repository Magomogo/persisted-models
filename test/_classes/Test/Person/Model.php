<?php
namespace Test\Person;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\ModelInterface;
use Test\Keymarker\Model as Keymarker;

class Model implements ModelInterface
{
    /**
     * @var Properties
     */
    private $properties;

    /**
     * @param string $id
     * @return Properties
     */
    public static function newProperties($id = null)
    {
        return new Properties($id);
    }

    /**
     * @param ContainerInterface $container
     * @return Properties
     */
    public function propertiesFor($container)
    {
        return $this->properties;
    }

//----------------------------------------------------------------------------------------------------------------------

    /**
     * @param Properties $properties
     * @param array $tags array of \Keymarker\Model
     */
    public function __construct($properties, array $tags = array())
    {
        $this->properties = $properties;
        $this->properties->tags = $tags;
    }

    public function politeTitle()
    {
        return $this->properties->title . ' ' . $this->properties->firstName . ' ' . $this->properties->lastName;
    }

    public function contactInfo()
    {
        return 'Phone: ' . $this->properties->phone . "\n" . 'Email: ' . $this->properties->email;
    }

    public function phoneNumberIsChanged($newNumber)
    {
        $this->properties->phone = $newNumber;
    }

    public function paymentInfo()
    {
        return $this->ableToPay() ?
            $this->properties->creditCard->paymentSystem() . ', ' . $this->properties->creditCard->maskedPan() : null;
    }

    public function ableToPay()
    {
        return !is_null($this->properties->creditCard);
    }

    public function tag(Keymarker $keymarker)
    {
        $this->properties->tags[] = $keymarker;
    }

    public function taggedAs()
    {
        return join(', ', $this->properties->tags);
    }

}
