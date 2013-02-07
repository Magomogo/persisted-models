<?php
namespace Employee;
use Person\Model as Person;
use Company\Model as Company;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Magomogo\Model\PropertyBag;
use CreditCard\Model as CreditCard;

class Model extends Person
{
    /**
     * @var \Company\Model
     */
    private $company;

    /**
     * @param $id
     * @param null $valuesToSet
     * @return \Magomogo\Model\PropertyBag
     */
    public static function propertiesSample($id = null, $valuesToSet = null)
    {
        return new PropertyBag(
            'employee',
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
            array(
                'company' => Company::propertiesSample()
            ),
            $valuesToSet
        );
    }

    /**
     * @param \Company\Model $company
     * @param PropertyBag $properties
     */
    public function __construct($company, $properties)
    {
        parent::__construct($properties);
        $this->company = $company;
    }

    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return \Employee\Model
     */
    public static function loadFrom($container, $id)
    {
        $loadedProperties = $container->loadProperties(self::propertiesSample($id));
        return new self(
            Company::loadFrom($container, $loadedProperties->reference('company')->id),
            $loadedProperties
        );
    }

    public function greeting()
    {
        return $this->politeTitle() . ' from ' . $this->company->name();
    }

    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @return string
     */
    public function putIn($container)
    {
        return $container->saveProperties($this->properties)->id;
    }
}
