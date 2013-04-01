<?php
namespace Magomogo\Persisted\PropertyContainer;

use Test\ObjectMother;
use Employee\Model as Employee;
use CreditCard\Model as CreditCard;
use Magomogo\Persisted\ContainerReadyInterface;

class MemoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider modelsProvider
     */
    public function testCanBePutInAndLoadedFrom(ContainerReadyInterface $model)
    {
        $container = new Memory;

        $id = $model->putIn($container);

        $this->assertEquals(
            $model,
            $model::loadFrom($container, $id)
        );
    }

    public static function modelsProvider()
    {
        return array(
            array(ObjectMother\CreditCard::datatransTesting()),
            array(ObjectMother\Person::maxim()),
            array(ObjectMother\Company::xiag()),
            array(ObjectMother\Keymarker::friend()),
            array(ObjectMother\Employee::maxim())
        );
    }

    public function testBehavesCorrectlyWhenEmpty()
    {
        $this->setExpectedException('Magomogo\\Persisted\\Exception\\NotFound');
        Employee::loadFrom(new Memory, null);
    }

    public function testDelete()
    {
        $container = new Memory;
        $cc = ObjectMother\CreditCard::datatransTesting();
        $cc->putIn($container);
        $cc->deleteFrom($container);

        $this->setExpectedException('Magomogo\\Persisted\\Exception\\NotFound');
        CreditCard::loadFrom($container, null);
    }

}
