<?php
namespace Person;
use Model\DataContainer\ContainerInterface;
use Model\ContainerReadyInterface;
use Company;
use Employee;

class Model implements ContainerReadyInterface
{
    use \Model\ContainerUtils;

    /**
     * @var Properties
     */
    protected $properties;

    public static function loadFrom(ContainerInterface $container, $id)
    {
        $properties = new Properties($id);
        $container->loadProperties($properties);
        return new self($properties);
    }

    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
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
}
