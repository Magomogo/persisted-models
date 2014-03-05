<?php

namespace Magomogo\Persisted\Test\Person;

use Magomogo\Persisted\Container\SqlDb;
use Magomogo\Persisted\Test\DbFixture;
use Magomogo\Persisted\Test\DbNames;
use Magomogo\Persisted\Test\ObjectMother;

class PersonInContainerTest extends \PHPUnit_Framework_TestCase
{
    private $fixture;

    private $propertiesId;

    private $person;

    protected function setUp()
    {
        $this->fixture = DbFixture::inMemory();
        $this->fixture->install();

        $this->person = ObjectMother\Person::maxim();
        $this->propertiesId = $this->person->save($this->dbContainer());
    }

    public function testCanBeLoadedFromContainer()
    {
        $loadedProps = new Properties();
        $loadedProps->loadFrom($this->dbContainer(), $this->propertiesId);

        $this->assertEquals(
            $this->person,
            new Model($loadedProps)
        );
    }

    private function dbContainer()
    {
        return new SqlDb($this->fixture->db, new DbNames);
    }
}
