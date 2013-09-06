<?php
namespace Magomogo\Persisted;

use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\Test\DbFixture;
use Magomogo\Persisted\Container\SqlDb;
use Magomogo\Persisted\Test\DbNames;
use Magomogo\Persisted\Test\Keymarker;
use Magomogo\Persisted\Test\ObjectMother;
use Magomogo\Persisted\Test\Company;
use Magomogo\Persisted\Test\Employee;
use Magomogo\Persisted\Test\JobRecord;

class ModelsPersistencyTest extends \PHPUnit_Framework_TestCase
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
     *
     * @param ModelInterface|callable $model
     */
    public function testCanBePutInAndLoadedFrom($model)
    {
        if (is_callable($model)) {
            $model = $model($this->dbContainer());
        }

        $id = $model->save($this->dbContainer());
        $this->assertEquals($model, $model::load($this->dbContainer(), $id));
    }

    /**
     * @dataProvider modelsProvider
     *
     * @param ModelInterface|callable $model
     */
    public function testAModelCanBeDeletedFromContainer($model)
    {
        if (is_callable($model)) {
            $model = $model($this->dbContainer());
        }

        if (method_exists($model, 'deleteFrom')) {
            $id = $model->save($this->dbContainer());
            $model->deleteFrom($this->dbContainer());

            $this->setExpectedException('Magomogo\\Persisted\\Exception\\NotFound');
            $model::load($this->dbContainer(), $id);
        }
    }

    public static function modelsProvider()
    {
        return array(
            array(ObjectMother\CreditCard::datatransTesting()),
            array(ObjectMother\Person::maxim()),
            array(ObjectMother\Person::maximWithoutCC()),
            array(ObjectMother\Company::xiag()),
            array(ObjectMother\Keymarker::friend()),
            array(array(__CLASS__, 'employeeModel')),
            array(array(__CLASS__, 'jobRecord')),
            array(array(__CLASS__, 'personHavingKeymarkers')),
        );
    }

//----------------------------------------------------------------------------------------------------------------------

    private function dbContainer()
    {
        return new SqlDb($this->fixture->db, new DbNames);
    }

    /**
     * @param ContainerInterface $container
     * @return Employee\Model
     */
    private static function employeeModel($container)
    {
        $company = ObjectMother\Company::xiag();
        $company->save($container);

        $keymarker = new Keymarker\Model(new Keymarker\Properties(array('id' => 'test')));
        $keymarker->save($container);

        return new Employee\Model(
            $company,
            new Employee\Properties(array('firstName' => 'John')),
            array($keymarker)
        );
    }

    private static function jobRecord($container)
    {
        $company1 = ObjectMother\Company::xiag();
        $company1->save($container);

        $company2 = ObjectMother\Company::nstu();
        $company2->save($container);

        return new JobRecord\Model($company1, $company2);
    }

    private static function personHavingKeymarkers($container)
    {
        $friend = ObjectMother\Keymarker::friend();
        $friend->save($container);
        $IT = ObjectMother\Keymarker::IT();
        $IT->save($container);

        $person = ObjectMother\Person::maxim();
        $person->tag($friend);
        $person->tag($IT);

        return $person;
    }
}
