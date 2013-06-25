<?php
namespace Magomogo\Persisted;

use Test\DbFixture;
use Magomogo\Persisted\Container\Db;
use Test\ObjectMother;
use Test\Employee\Model as Employee;

class ModelsTest extends \PHPUnit_Framework_TestCase
{
    private $fixture;

    protected function setUp()
    {
        $this->fixture = new DbFixture();
        $this->fixture->install();
    }

    /**
     * @dataProvider modelsProvider
     */
    public function testCanBePutInAndLoadedFrom(ModelInterface $model)
    {
        $id = $model->propertiesFrom($this->dbContainer())->putIn($this->dbContainer());
        $this->assertEquals($model, $model::load($this->dbContainer(), $id));
    }

    public function testEmployeeModel()
    {
        $employee = ObjectMother\Employee::maxim();
        $employee->propertiesFrom($this->dbContainer())->foreign()->company->putIn($this->dbContainer());
        $id = $employee->propertiesFrom($this->dbContainer())->putIn($this->dbContainer());

        $this->assertEquals($employee, Employee::load($this->dbContainer(), $id));
    }

    public static function modelsProvider()
    {
        return array(
            array(ObjectMother\CreditCard::datatransTesting()),
            array(ObjectMother\Person::maxim()),
            array(ObjectMother\Person::maximWithoutCC()),
            array(ObjectMother\Company::xiag()),
            array(ObjectMother\Keymarker::friend()),
        );
    }

    private function dbContainer()
    {
        return new Db($this->fixture->db, 'Test\\');
    }
}
