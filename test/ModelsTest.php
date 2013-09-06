<?php
namespace Magomogo\Persisted;

use Magomogo\Persisted\Test\DbFixture;
use Magomogo\Persisted\Container\SqlDb;
use Magomogo\Persisted\Test\DbNames;
use Magomogo\Persisted\Test\Keymarker;
use Magomogo\Persisted\Test\ObjectMother;
use Magomogo\Persisted\Test\Company;
use Magomogo\Persisted\Test\Employee;

class ModelsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DbFixture
     */
    private $fixture;

    protected function setUp()
    {
        $this->fixture = DbFixture::inMemory();
        $this->fixture->install();
    }

    /**
     * @dataProvider modelsProvider
     */
    public function testCanBePutInAndLoadedFrom(ModelInterface $model)
    {
        $id = $model->save($this->dbContainer());
        $this->assertEquals($model, $model::load($this->dbContainer(), $id));
    }

    public function testEmployeeModel()
    {
        $company = ObjectMother\Company::xiag();
        $company->save($this->dbContainer());

        $keymarker = new Keymarker\Model(new Keymarker\Properties(array('id' => 'test')));
        $keymarker->save($this->dbContainer());

        $employee = new Employee\Model(
            $company,
            new Employee\Properties(array('firstName' => 'John')),
            array($keymarker)
        );

        $id = $employee->save($this->dbContainer());

        $this->assertEquals(
            $employee,
            Employee\Model::load($this->dbContainer(), $id)
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
        return new SqlDb($this->fixture->db, new DbNames);
    }
}
