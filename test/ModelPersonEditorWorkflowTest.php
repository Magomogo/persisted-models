<?php

namespace Magomogo\Persisted;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\Container\SqlDb;
use Magomogo\Persisted\Container\Memory;
use Magomogo\Persisted\Test\DbFixture;
use Magomogo\Persisted\Test\DbNames;
use Magomogo\Persisted\Test\ObjectMother;
use Magomogo\Persisted\Test\Person;

class ModelEditorWorkflowTest extends \PHPUnit_Framework_TestCase
{
    private $fixture;

    private $propertiesId;

    protected function setUp()
    {
        $this->fixture = DbFixture::inMemory();
        $this->fixture->install();

        $this->propertiesId = ObjectMother\Person::maxim()->save($this->dbContainer());
    }

    public function testCanCreateANewModel()
    {
        $editor = new PersonEditor();
        $editor->edit(array(
                'firstName' => 'John',
                'lastName' => 'Doe',
            ));
        $id = $editor->saveTo($this->dbContainer());

        $this->assertEquals(
            'Mr. John Doe',
            Person\Model::load($this->dbContainer(), $id)->politeTitle()
        );
    }

    public function testCanBeEditWithSomeEditor()
    {
        $model = Person\Model::load($this->dbContainer(), $this->propertiesId);
        $editor = new PersonEditor($model);

        $editor->edit(array(
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'John.Doe@example.com',
                'creditCard' => array(
                    'system' => 'VISA',
                    'pan' => '9512456785213698',
                )
            ));
        $editor->saveTo($this->dbContainer());

        $updatedModel = Person\Model::load($this->dbContainer(), $this->propertiesId);

        $this->assertEquals('Mr. John Doe', $updatedModel->politeTitle());
        $this->assertEquals('VISA, 9512 **** **** 3698', $updatedModel->paymentInfo());
    }

//----------------------------------------------------------------------------------------------------------------------

    private function dbContainer()
    {
        return new SqlDb($this->fixture->db, new DbNames);
    }
}


//======================================================================================================================

class PersonEditor extends Memory
{
    private $properties;

    /**
     * @param Person\Model|null $person
     */
    public function __construct($person = null)
    {
        $this->properties = $this->exposeProperties($person ?: self::newPerson());
    }

    public function edit($data)
    {
        $this->editProperties($data, $this->properties);
    }

    /**
     * @param ContainerInterface $container
     * @return string
     */
    public function saveTo($container)
    {
        return $this->properties->putIn($container);
    }

    private static function newPerson()
    {
        return new Person\Model(new Person\Properties(array('title' => 'Mr.')));
    }

    /**
     * @param $data
     * @param AbstractProperties $properties
     */
    private function editProperties($data, $properties)
    {
        foreach ($data as $name => $value) {
            if (is_array($value)) {
                $this->editProperties($value, $this->exposeProperties($properties->$name));
            } else {
                $properties->$name = $value;
            }
        }
    }

}