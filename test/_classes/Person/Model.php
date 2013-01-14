<?php
namespace Person;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Magomogo\Model\ContainerReadyAbstract;
use Company;
use Employee;
use Keymarker;

class Model extends ContainerReadyAbstract
{
    /**
     * @var array
     */
    private $tags = array();

    public static function loadFrom(ContainerInterface $container, $id)
    {
        $properties = new Properties($id);
        $container->loadProperties($properties);

        $person = new self($properties);
        foreach ($container->listReferences('person2keymarker', $properties, 'Keymarker\Properties')
                 as $keymarkerProperties) {
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
        /** @var \Magomogo\Model\ContainerReadyInterface $keymarker */
        foreach ($this->tags as $keymarker) {
            $connectedProperties[] = $keymarker->propertiesFrom($container);
        }

        $container->referToMany('person2keymarker', $this->properties, $connectedProperties);
        return $this->properties->id;
    }

}
