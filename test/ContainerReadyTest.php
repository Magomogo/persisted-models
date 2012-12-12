<?php
use Test\DbFixture;
use Magomogo\Model\PropertyContainer\Db;
use Magomogo\Model\ContainerReadyInterface;
use Test\ObjectMother;

class ContainerReadyTest extends PHPUnit_Framework_TestCase
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
    public function testCanBePutInAndLoadedFrom(ContainerReadyInterface $model)
    {
        $id = $model->putIn($this->dbContainer());
        $this->assertEquals(
            $model,
            $model::loadFrom($this->dbContainer(), $id)
        );
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

    private function dbContainer()
    {
        return new Db($this->fixture->db);
    }
}
