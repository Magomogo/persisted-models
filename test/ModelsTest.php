<?php
namespace Magomogo\Persisted;

use PHPUnit\Framework\TestCase;
use Test\DbFixture;
use Magomogo\Persisted\Container\Db;
use Test\ObjectMother;
use Test\Company;
use Test\Employee;

class ModelsTest extends TestCase
{
    private $fixture;

    protected function setUp(): void
    {
        $this->fixture = new DbFixture();
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
        $props = ObjectMother\Employee::maximProperties();
        $props->foreign()->company->putIn($this->dbContainer());
        $id = $props->putIn($this->dbContainer(), $props->foreign()->company);

        $this->assertEquals(
            new Employee\Model(new Company\Model($props->foreign()->company), $props, $props->tags),
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
        return new Db($this->fixture->db, 'Test\\');
    }
}
