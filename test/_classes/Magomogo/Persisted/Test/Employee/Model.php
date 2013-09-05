<?php
namespace Magomogo\Persisted\Test\Employee;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\Test\Person\Model as Person;
use Magomogo\Persisted\Test\Company;

class Model extends Person
{
    /**
     * @var Properties
     */
    protected $properties;

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
        $p->loadFrom($container, $id);
        return new self(new Company\Model($p->foreign()->company), $p, $p->tags);
    }

    public function save($container)
    {
        $this->company->isOwner($this->properties);
        return $this->properties->putIn($container);
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
