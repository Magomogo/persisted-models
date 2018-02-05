<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\Test\Person;
use Magomogo\Persisted\Test\Keymarker;
use Magomogo\Persisted\Test\ObjectMother;
use Magomogo\Persisted\Test\Employee\Model as Employee;
use Magomogo\Persisted\Test\CreditCard\Model as CreditCard;
use Magomogo\Persisted\Test\JobRecord;
use Magomogo\Persisted\Test\Affiliate;

class MemoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param ModelInterface $model
     * @dataProvider modelsProvider
     */
    public function testCanBePutInAndLoadedFrom(ModelInterface $model)
    {
        $container = new Memory;
        $id = $model->save($container);

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
        $id = $employee->save($container);

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
        $id = $cc->save($container);
        $cc->deleteFrom($container);

        $this->setExpectedException('Magomogo\\Persisted\\Exception\\NotFound');
        CreditCard::load($container, $id);
    }

    public function testCanSaveAndLoadTwoModelsOfSameType()
    {
        $container = new Memory;

        $id1 = ObjectMother\Keymarker::friend()->save($container);
        $id2 = ObjectMother\Keymarker::IT()->save($container);

        $this->assertEquals('Friend', strval(Keymarker\Model::load($container, $id1)));
        $this->assertEquals('IT', strval(Keymarker\Model::load($container, $id2)));
    }

    public function testStoresPersonKeymarkers()
    {
        $container = new Memory;

        $person = ObjectMother\Person::maxim();
        $person->tag(ObjectMother\Keymarker::friend());
        $person->tag(ObjectMother\Keymarker::IT());

        $id = $person->save($container);

        $this->assertEquals(
            $person,
            $person::load($container, $id)
        );
    }

    public function testCanQueryForStoredProperties()
    {
        $container = new Memory;
        $properties = ObjectMother\Person::maximProperties();
        $properties->putIn($container);
        $this->assertSame(
            $properties,
            $container->exposeProperties(new Person\Model($properties))
        );
    }

    public function testCanSaveAndLoadAJobRecord()
    {
        $container = new Memory;

        $jobRecord = new JobRecord\Model(ObjectMother\Company::xiag(), ObjectMother\Company::nstu());
        $id = $jobRecord->save($container);

        $this->assertEquals(
            $jobRecord,
            JobRecord\Model::load($container, $id)
        );
    }

    public function testProvidesCorrectInstanceOfAggregatedProperties()
    {
        $container = new Memory;
        $person = ObjectMother\Person::maxim();
        $person->save($container);

        $personProperties = $container->exposeProperties($person);
        $personProperties->creditCard->save($container);
        $creditCardProperties = $container->exposeProperties($personProperties->creditCard);
        $creditCardProperties->system = 'I\'ve updated it!';

        $this->assertSame(
            'I\'ve updated it!',
            $personProperties->creditCard->paymentSystem()
        );
    }

    public function testStoreModelsWithEqualsIDsProperly()
    {
        $cookieProps = new Affiliate\Cookie\Properties(array('id' => 1, 'lifeTime' => 60));
        $cookieModel = new Affiliate\Cookie\Model($cookieProps);

        $affiliateProps = new Affiliate\Properties(array(
            'id' => 1,
            'name' => 'STS Shop',
            'cookie' => $cookieModel
        ));
        $affiliateModel = new Affiliate\Model($affiliateProps);

        $container = new Memory;
        $id = $affiliateModel->save($container);

        $loadedModel = Affiliate\Model::load($container, $id);
        $this->assertEquals($affiliateModel, $loadedModel);
    }
}
