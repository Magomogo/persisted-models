<?php
namespace Person;
use Model\PropertyContainer\ContainerInterface;
use Model\ContainerReadyInterface;
use Company;
use Employee;
use Keymarker;

class Model implements ContainerReadyInterface
{
    use \Model\ContainerUtils;

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

        $person = new self($properties);
        foreach ($container->listConnections($properties, new Keymarker\Properties()) as $keymarkerProperties) {
            $person->tag(new Keymarker\Model($keymarkerProperties));
        }
        return $person;
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
        $container->saveProperties($this->properties);

        $connectedProperties = array();
        /** @var ContainerReadyInterface $keymarker */
        foreach ($this->tags as $keymarker) {
            $connectedProperties[] = $keymarker->confirmOrigin($container);
        }

        $container->connectToMany($this->properties, $connectedProperties);
        return $this->properties->id;
    }

}
