<?php
namespace Test\Employee;

use Test\Person\Model as Person;
use Test\Company\Model as Company;
use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\PropertyBag;

class Model extends Person
{
    /**
     * @var \Test\Company\Model
     */
    private $company;

    /**
     * @param \Test\Company\Model $company
     * @param PropertyBag $properties
     */
    public function __construct($company, $properties)
    {
        parent::__construct($properties);
        $this->company = $company;
    }

    /**
     * @param \Magomogo\Persisted\Container\ContainerInterface $container
     * @param string $id
     * @return \Test\Employee\Model
     */
    public static function loadFrom($container, $id)
    {
        $loadedProperties = $container->loadProperties(new Properties($id));
        return new self(
            Company::loadFrom($container, $loadedProperties->foreign()->company->id),
            $loadedProperties
        );
    }

    public function greeting()
    {
        return $this->politeTitle() . ' from ' . $this->company->name();
    }

    /**
     * @param \Magomogo\Persisted\Container\ContainerInterface $container
     * @return string
     */
    public function putIn($container)
    {
        return $container->saveProperties($this->properties)->id;
    }
}
