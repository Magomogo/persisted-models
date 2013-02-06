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

    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return \Employee\Model
     */
    public static function loadFrom($container, $id)
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

    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @return string
     */
    public function putIn($container)
    {
        return $container->saveProperties(
            $this->properties,
            array('company' => $this->company->propertiesFrom($container))
        )->id;
    }
}
