<?php
namespace Magomogo\Persisted\Container;

use Test\ObjectMother;
use Test\Employee\Model as Employee;
use Test\CreditCard\Model as CreditCard;
use Magomogo\Persisted\PersistedInterface;

class MemoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider modelsProvider
     */
    public function testCanBePutInAndLoadedFrom(PersistedInterface $model)
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
