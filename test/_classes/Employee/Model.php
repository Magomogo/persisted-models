<?php
namespace Employee;
use Person;
use Company;
use Magomogo\Model\PropertyContainer\ContainerInterface;

class Model extends Person\Model
{
    /**
     * @var Company\Model
     */
    private $company;

    public static function loadFrom(ContainerInterface $container, $id)
    {
        $properties = new \Person\Properties($id);
        $references = array('company' => new \Company\Properties());
        $container->loadProperties($properties, $references);
        return new self(new Company\Model($references['company']), $properties);
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

    public function putIn(ContainerInterface $container)
    {
        return $container->saveProperties(
            $this->properties,
            array('company' => $this->company->propertiesFrom($container))
        )->id;
    }

    public function propertiesFrom(ContainerInterface $container)
    {
        return $this->properties->assertOriginIs($container);
    }
}
