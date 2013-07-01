<?php
namespace Test\Employee;

use Magomogo\Persisted\Container\ContainerInterface;
use Test\Person\Model as Person;
use Test\Company;

class Model extends Person
{
    /**
     * @var Company\Model
     */
    private $company;

    /**
     * @param ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function load($container, $id)
    {
        $p = new Properties();
        $p->persisted($id, $container);
        $p->loadFrom($container);
        return new self(new Company\Model($p->foreign()->company), $p, $p->tags);
    }

    public function save($container)
    {
        return $this->properties->putIn(
            $container,
            $this->company->propertiesToBeConnectedWith($this->properties)
        );
    }

    /**
     * @param Company\Model $company
     * @param Properties $properties
     * @param array $tags
     * @return Model
     */
    public function __construct($company, $properties, array $tags = array())
    {
        parent::__construct($properties, $tags);
        $this->company = $company;
    }

    public function greeting()
    {
        return $this->politeTitle() . ' from ' . $this->company->name();
    }
}
