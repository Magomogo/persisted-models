<?php
namespace Magomogo\Persisted;

use Test\DbFixture;
use Magomogo\Persisted\Container\Db;
use Magomogo\Persisted\PersistedInterface;
use Test\ObjectMother;
use Test\Employee\Model as Employee;
use Test\Company\Model as Company;

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
    public function testCanBePutInAndLoadedFrom(PersistedInterface $model)
    {
        $id = $model->putIn($this->dbContainer());
        $this->assertEquals(
            $model,
            $model::loadFrom($this->dbContainer(), $id)
        );
    }

    public function testEmployeeModel()
    {
        $properties = ObjectMother\Employee::maximProperties();

        $persistedCompany = new Company($properties->foreign()->company);
        $persistedCompany->putIn($this->dbContainer());

        $employee = new Employee($persistedCompany, $properties);
        $id = $employee->putIn($this->dbContainer());

        $this->assertEquals(
            $employee,
            Employee::loadFrom($this->dbContainer(), $id)
        );
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
