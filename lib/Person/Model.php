<?php
namespace Person;
use Model\DataContainer\ContainerInterface;
use Model\ContainerReadyInterface;
use Company;
use Employee;
use Keymarker;

class Model implements ContainerReadyInterface
{
    /**
     * @var Properties
     */
    protected $properties;

    /**
     * @var array
     */
    private $tags = array();

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

    public function tag(Keymarker\Model $keymarker)
    {
        $this->tags[] = $keymarker;
    }

    public function taggedAs()
    {
        return join(', ', $this->tags);
    }

    public function putIn(ContainerInterface $container)
    {
        return $container->saveProperties($this->properties)->id;
    }

    public function confirmOrigin(ContainerInterface $container)
    {
        return $this->properties->confirmOrigin($container);
    }
}
