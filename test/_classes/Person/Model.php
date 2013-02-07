<?php
namespace Person;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Magomogo\Model\ContainerReadyAbstract;
use Keymarker\Model as Keymarker;
use Magomogo\Model\PropertyBag;
use CreditCard\Model as CreditCard;

class Model extends ContainerReadyAbstract
{
    /**
     * @var array
     */
    private $tags = array();

    /**
     * @param $id
     * @param null $valuesToSet
     * @return \Magomogo\Model\PropertyBag
     */
    public static function propertiesSample($id = null, $valuesToSet = null)
    {
        return new PropertyBag(
            'person',
            $id,
            array(
                'title' => '',
                'firstName' => '',
                'lastName' => '',
                'phone' => '',
                'email' => '',
                'creditCard' => new CreditCard(CreditCard::propertiesSample()),
                'birthDay' => new \DateTime('1970-01-01')
            ),
            array(),
            $valuesToSet
        );
    }

    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return \Person\Model
     */
    public static function loadFrom($container, $id)
    {
        $properties = $container->loadProperties(self::propertiesSample($id));

        $tags = array();
        foreach ($container->listReferences('person2keymarker', $properties, Keymarker::propertiesSample())
                 as $keymarkerProperties) {
            $tags[] = new Keymarker($keymarkerProperties);
        }
        return new self($properties, $tags);
    }

    /**
     * @param PropertyBag $properties
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
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @return string
     */
    public function putIn($container)
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
