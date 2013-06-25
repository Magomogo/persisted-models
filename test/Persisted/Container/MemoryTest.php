<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\ModelInterface;
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
        $id = $model->properties()->putIn($container);

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
        $id = $employee->properties()->putIn($container);

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
        $cc->properties()->putIn($container);
        $cc->properties()->deleteFrom($container);

        $this->setExpectedException('Magomogo\\Persisted\\Exception\\NotFound');
        CreditCard::load($container, $cc->properties()->id($container));
    }

    public function testCanSaveAndLoadTwoModelsOfSameType()
    {
        $container = new Memory;

        $id1 = ObjectMother\Keymarker::friend()->properties()->putIn($container);
        $id2 = ObjectMother\Keymarker::IT()->properties()->putIn($container);

        $this->assertEquals('Friend', Keymarker\Model::load($container, $id1));
        $this->assertEquals('IT', Keymarker\Model::load($container, $id2));
    }

    public function testStoresPersonKeymarkers()
    {
        $container = new Memory;

        $person = ObjectMother\Person::maxim();
        $person->tag(ObjectMother\Keymarker::friend());
        $person->tag(ObjectMother\Keymarker::IT());

        $id = $person->properties()->putIn($container);

        $this->assertEquals(
            $person,
            $person::load($container, $id)
        );
    }

    public function testCanQueryForStoredProperties()
    {
        $container = new Memory;
        $properties = ObjectMother\Person::maxim()->properties();
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
        $id = ObjectMother\Person::maxim()->properties()->putIn($container);

        $this->assertSame(
            $container->query('Test\\Person\\Properties', $id),
            $container->query('Test\\Person\\Properties', $id)
        );
    }
}
