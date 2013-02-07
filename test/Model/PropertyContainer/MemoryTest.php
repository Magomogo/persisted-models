<?php
namespace Magomogo\Model\PropertyContainer;

use Test\ObjectMother;
use Magomogo\Model\ContainerReadyInterface;

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
            $model->newFrom($container, $id)
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
        $this->setExpectedException('Magomogo\\Model\\Exception\\NotFound');
        ObjectMother\Employee::maxim()->newFrom(new Memory, null);
    }

    public function testDelete()
    {
        $container = new Memory;
        $cc = ObjectMother\CreditCard::datatransTesting();
        $cc->putIn($container);
        $cc->deleteFrom($container);

        $this->setExpectedException('Magomogo\\Model\\Exception\\NotFound');
        $cc->newFrom($container, null);
    }

}
