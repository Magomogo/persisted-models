<?php
namespace Employee;
use Person;
use Company;
use Model\DataContainer\ContainerInterface;

class Model extends Person\Model
{
    /**
     * @var Company\Model
     */
    private $company;

    public static function loadFrom(ContainerInterface $container, $id)
    {
        $properties = new \Person\Properties($id);
        $references = $container->loadProperties($properties);
        return new self(new Company\Model($references['company_properties']), $properties);
    }

    public function __construct(Company\Model $company, Person\Properties $properties)
    {
        parent::__construct($properties);
        $this->company = $company;
    }

    public function greeting()
    {
        return $this->politeTitle() . ' from ' . $this->company->name();
    }
}
