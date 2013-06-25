<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\ModelInterface;
use Test\Company;
use Test\Keymarker;
use Test\ObjectMother;
use Test\Employee\Model as Employee;
use Test\CreditCard\Model as CreditCard;
use Test\JobRecord;

class MemoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider modelsProvider
     */
    public function testCanBePutInAndLoadedFrom(ModelInterface $model)
    {
        $container = new Memory;
        $id = $model->putIn($container);

        $this->assertEquals($model, $model::load($container, $id));
    }

    public static function modelsProvider()
    {
        return array(
            array(ObjectMother\CreditCard::datatransTesting()),
            array(ObjectMother\Person::maxim()),
            array(ObjectMother\Company::xiag()),
            array(ObjectMother\Keymarker::friend()),
        );
    }

    public function testEmployeeModel()
    {
        $container = new Memory;

        $employee = ObjectMother\Employee::maxim();
        $id = $employee->putIn($container);

        $this->assertEquals($employee, Employee::load($container, $id));
    }

    public function testBehavesCorrectlyWhenEmpty()
    {
        $this->setExpectedException('Magomogo\\Persisted\\Exception\\NotFound');
        Employee::load(new Memory, '12');
    }

    public function testDelete()
    {
        $container = new Memory;
        $cc = ObjectMother\CreditCard::datatransTesting();
        $id = $cc->putIn($container);
        $cc->deleteFrom($container);

        $this->setExpectedException('Magomogo\\Persisted\\Exception\\NotFound');
        CreditCard::load($container, $id);
    }

    public function testCanSaveAndLoadTwoModelsOfSameType()
    {
        $container = new Memory;

        $id1 = ObjectMother\Keymarker::friend()->putIn($container);
        $id2 = ObjectMother\Keymarker::IT()->putIn($container);

        $this->assertEquals('Friend', strval(Keymarker\Model::load($container, $id1)));
        $this->assertEquals('IT', strval(Keymarker\Model::load($container, $id2)));
    }

    public function testStoresPersonKeymarkers()
    {
        $container = new Memory;

        $person = ObjectMother\Person::maxim();
        $person->tag(ObjectMother\Keymarker::friend());
        $person->tag(ObjectMother\Keymarker::IT());

        $id = $person->putIn($container);

        $this->assertEquals(
            $person,
            $person::load($container, $id)
        );
    }

    public function testCanQueryForStoredProperties()
    {
        $container = new Memory;
        $properties = ObjectMother\Person::maximProperties();
        $id = $properties->putIn($container);
        $this->assertEquals(
            $properties,
            $container->query('Test\\Person\\Properties', $id)
        );
        $this->assertNull($container->query('wrong_type', $id));
        $this->assertNull($container->query('Test\\Person\\Properties', 'wrong_id'));
    }

    public function testQueryExposesStoredPropertiesInstance() {
        $container = new Memory;
        $id = ObjectMother\Person::maxim()->putIn($container);

        $this->assertSame(
            $container->query('Test\\Person\\Properties', $id),
            $container->query('Test\\Person\\Properties', $id)
        );
    }

    public function testCanSaveAndLoadAJobRecord()
    {
        $container = new Memory;

        $jobRecordProps = new JobRecord\Properties();
        $jobRecordProps->foreign()->currentCompany = new Company\Properties(array('name' => 'XIAG'));
        $jobRecordProps->foreign()->previousCompany = new Company\Properties(array('name' => 'NSTU'));

        $id = $jobRecordProps->putIn($container);

        $this->assertEquals(
            new JobRecord\Model(
                new Company\Model($jobRecordProps->foreign()->currentCompany),
                new Company\Model($jobRecordProps->foreign()->previousCompany),
                $jobRecordProps
            ),
            JobRecord\Model::load($container, $id)
        );
    }

}
