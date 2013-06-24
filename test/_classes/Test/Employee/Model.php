<?php
namespace Test\Employee;

use Test\Person\Model as Person;
use Test\Company;

class Model extends Person
{
    /**
     * @var Company\Model
     */
    private $company;

    /**
     * @param string $id
     * @return Properties
     */
    public static function newProperties($id = null)
    {
        return new Properties($id);
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
