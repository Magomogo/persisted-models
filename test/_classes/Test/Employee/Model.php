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
    public static function load($container, $id = null)
    {
        $p = new Properties();
        $p->persisted($id, $container);
        return $p->loadFrom($container)->constructModel();
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
