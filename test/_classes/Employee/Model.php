<?php
namespace Employee;
use Person\Model as Person;
use Company\Model as Company;
use Magomogo\Model\PropertyContainer\ContainerInterface;

class Model extends Person
{
    /**
     * @var \Company\Model
     */
    private $company;

    public function __construct(Company $company, Properties $properties)
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
        $loadedProperties = Properties::loadFrom($container, $id);
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
