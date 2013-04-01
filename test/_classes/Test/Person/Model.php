<?php
namespace Test\Person;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\PersistedAbstract;
use Test\Keymarker\Model as Keymarker;
use Test\Keymarker\Properties as KeymarkerProperties;
use Magomogo\Persisted\PropertyBag;

class Model extends PersistedAbstract
{
    /**
     * @var array
     */
    private $tags = array();

    /**
     * @param \Magomogo\Persisted\Container\ContainerInterface $container
     * @param string $id
     * @return \Test\Person\Model
     */
    public static function loadFrom($container, $id)
    {
        $properties = $container->loadProperties(new Properties($id));

        $tags = array();
        foreach ($container->listReferences('person2keymarker', $properties, new KeymarkerProperties)
                 as $keymarkerProperties) {
            $tags[] = Keymarker::loadFrom($container, $keymarkerProperties->id);
        }
        return new self($properties, $tags);
    }

    /**
     * @param Properties $properties
     * @param array $tags array of \Keymarker\Model
     */
    public function __construct($properties, array $tags = array())
    {
        $this->properties = $properties;
        $this->tags = $tags;
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
        $this->tags[] = $keymarker;
    }

    public function taggedAs()
    {
        return join(', ', $this->tags);
    }

    /**
     * @param \Magomogo\Persisted\Container\ContainerInterface $container
     * @return string
     */
    public function putIn($container)
    {
        $container->saveProperties($this->properties);

        $connectedProperties = array();
        /** @var \Magomogo\Persisted\PersistedInterface $keymarker */
        foreach ($this->tags as $keymarker) {
            $connectedProperties[] = $keymarker->propertiesFrom($container);
        }

        $container->referToMany('person2keymarker', $this->properties, $connectedProperties);
        return $this->properties->id;
    }

}
