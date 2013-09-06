<?php
namespace Magomogo\Persisted\Test\Person;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\Test\Keymarker\Model as Keymarker;

class Model implements ModelInterface
{
    /**
     * @var Properties
     */
    protected $properties;

    /**
     * @param ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function load($container, $id)
    {
        $p = new Properties();
        $p->loadFrom($container, $id);
        return new self($p, $p->tags);
    }

    public function save($container)
    {
        return $this->properties->putIn($container);
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

    public function lastName()
    {
        return $this->properties->lastName;
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