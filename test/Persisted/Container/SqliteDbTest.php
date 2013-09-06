<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\Test\DbFixture;
use Magomogo\Persisted\Test\DbNames;
use Magomogo\Persisted\Test\ObjectMother;
use Magomogo\Persisted\Container\Db;
use Magomogo\Persisted\Test\CreditCard;
use Magomogo\Persisted\Test\Person;
use Magomogo\Persisted\Test\JobRecord;
use Magomogo\Persisted\Test\Company;
use Magomogo\Persisted\Test\Employee;
use Mockery as m;

class SqliteDbTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DbFixture
     */
    private $fixture;

    protected function setUp()
    {
        $this->fixture = new DbFixture();
        $this->fixture->install();
    }

    public function testFixtureHasCorrectTablesCreated()
    {
        $this->assertEquals(array(), $this->fixture->db->fetchAll("SELECT * FROM company"));
        $this->assertEquals(array(), $this->fixture->db->fetchAll("SELECT * FROM person"));
        $this->assertEquals(array(), $this->fixture->db->fetchAll("SELECT * FROM creditcard"));
    }

    public function testSavesCreditCardIntoDatabase()
    {
        ObjectMother\CreditCard::datatransTesting()->save($this->sqliteContainer());

        $this->assertArraysEqualKeyCaseInsensitive(
            array(
                'id' => 1,
                'system' => 'VISA',
                'pan' => '9500000000000001',
                'validMonth' => '12',
                'validYear' => '2015',
                'ccv' => '234',
                'cardholderName' => 'Maxim Gnatenko'
            ),
            $this->fixture->db->fetchAssoc("SELECT * FROM creditcard")
        );
    }

    public function testSavesAPersonHavingCreditCardIntoDatabase()
    {
        ObjectMother\Person::maxim()->save($this->sqliteContainer());

        $this->assertArraysEqualKeyCaseInsensitive(
            array(
                'id' => 1,
                'company' => null,
                'title' => 'Mr.',
                'firstName' => 'Maxim',
                'lastName' => 'Gnatenko',
                'phone' => '+7923-117-2801',
                'email' => 'maxim@xiag.ch',
                'creditCard' => 1,
                'birthDay' => '1975-07-07',
            ),
            self::smoothBirthDayFormatDifference($this->fixture->db->fetchAssoc("SELECT * FROM person"))
        );

        $this->assertArraysEqualKeyCaseInsensitive(
            array(
                'id' => 1,
                'system' => 'VISA',
                'pan' => '9500000000000001',
                'validMonth' => '12',
                'validYear' => '2015',
                'ccv' => '234',
                'cardholderName' => 'Maxim Gnatenko'
            ),
            $this->fixture->db->fetchAssoc("SELECT * FROM creditcard")
        );
    }

    public function testReadsModelFromTheDatabase()
    {
        $maximId = ObjectMother\Person::maxim()->save($this->sqliteContainer());
        $this->assertEquals(
            ObjectMother\Person::maxim($maximId)->politeTitle(),
            Person\Model::load($this->sqliteContainer(), $maximId)->politeTitle()
        );
    }

    public function testCanUpdateModelInTheDatabase()
    {
        $maximId = ObjectMother\Person::maxim()->save($this->sqliteContainer());
        $maxim = Person\Model::load($this->sqliteContainer(), $maximId);

        $maxim->phoneNumberIsChanged('903-903');
        $maxim->save($this->sqliteContainer());

        $this->assertContains(
            '903-903',
            Person\Model::load($this->sqliteContainer(), $maximId)->contactInfo()
        );
    }

    public function testWritesEmployeePropertiesIntoPersonPropertiesTable()
    {
        $this->putEmployeeIn($this->sqliteContainer());

        $this->assertArraysEqualKeyCaseInsensitive(
            array(
                'id' => 1,
                'company' => 1,
                'title' => 'Mr.',
                'firstName' => 'Maxim',
                'lastName' => 'Gnatenko',
                'phone' => '+7923-117-2801',
                'email' => 'maxim@xiag.ch',
                'creditCard' => 1,
                'birthDay' => '1975-07-07'
            ),
            self::smoothBirthDayFormatDifference($this->fixture->db->fetchAssoc("SELECT * FROM person"))
        );
    }

    public function testReadsEmployeeModel()
    {
        $employee = $this->putEmployeeIn($this->sqliteContainer());
        $this->assertEquals(
            $employee,
            $employee::load($this->sqliteContainer(), 1)
        );
    }

    public function testCanSaveAndLoadAJobRecord()
    {
        $prop1 = new Company\Properties(array('name' => 'XIAG'));
        $prop1->putIn($this->sqliteContainer());
        $prop2 = new Company\Properties(array('name' => 'NSTU'));
        $prop2->putIn($this->sqliteContainer());

        $jobRecord = new JobRecord\Model(
            new Company\Model($prop1),
            new Company\Model($prop2)
        );

        $id = $jobRecord->save($this->sqliteContainer());

        $this->assertModelsAreEqual(
            $jobRecord,
            JobRecord\Model::load($this->sqliteContainer(), $id)
        );
    }

    public function testCreatesTwoRecordsOfSameType()
    {
        $this->persistTwoKeymarkers();
        $this->assertEquals('2', $this->fixture->db->fetchColumn('select count(1) from keymarker'));
    }

    private function persistTwoKeymarkers()
    {
        $persistedKeymarker1 = ObjectMother\Keymarker::friend();
        $persistedKeymarker1->save($this->sqliteContainer());
        $persistedKeymarker2 = ObjectMother\Keymarker::IT();
        $persistedKeymarker2->save($this->sqliteContainer());
        return array($persistedKeymarker1, $persistedKeymarker2);
    }

    public function testStoresPersonKeymarkers()
    {
        list ($persistedKeymarker1, $persistedKeymarker2) = $this->persistTwoKeymarkers();

        $person = ObjectMother\Person::maxim();
        $person->tag($persistedKeymarker1);
        $person->tag($persistedKeymarker2);

        $id = $person->save($this->sqliteContainer());

        $this->assertEquals(
            $person,
            $person::load($this->sqliteContainer(), $id)
        );
    }

    public function testWorksWithNulls()
    {
        $personProperties = new Person\Properties();
        $personProperties->firstName = 'Vova';
        $personProperties->lastName = null;

        $vova = new Person\Model($personProperties);
        $id = $vova->save($this->sqliteContainer());

        $lastNameCol = $this->fixture->db->quoteIdentifier('lastName');

        $this->assertNull(
            $this->fixture->db->fetchColumn("SELECT $lastNameCol FROM person WHERE id = ?", array($id))
        );

        $person = Person\Model::load($this->sqliteContainer(), $id);
        $this->assertNull($person->lastName());
    }

    public function testAModelCanBeDeletedFromContainer()
    {
        $cc = ObjectMother\CreditCard::datatransTesting();
        $id = $cc->save($this->sqliteContainer());

        $cc->deleteFrom($this->sqliteContainer());

        $this->setExpectedException('Magomogo\\Persisted\\Exception\\NotFound');
        CreditCard\Model::load($this->sqliteContainer(), $id);
    }

//----------------------------------------------------------------------------------------------------------------------

    private function putEmployeeIn($container)
    {
        $props = ObjectMother\Employee::maximProperties();
        $props->foreign()->company->putIn($container);
        $props->putIn($container, $props->foreign()->company);
        return new Employee\Model(new Company\Model($props->foreign()->company), $props);
    }

    private function sqliteContainer()
    {
        return new Db($this->fixture->db, new DbNames);
    }

    /**
     * @param ModelInterface $model1
     * @param ModelInterface $model2
     */
    private function assertModelsAreEqual($model1, $model2)
    {
        $container = new Memory();
        $this->assertEquals(
            $container->exposeProperties($model1)->resetPersistency(),
            $container->exposeProperties($model2)->resetPersistency()
        );
    }

    private function assertArraysEqualKeyCaseInsensitive($arr1, $arr2)
    {
        $this->assertEquals(
            array_change_key_case($arr1), array_change_key_case($arr2)
        );
    }

    private static function smoothBirthDayFormatDifference($row)
    {
        $row['birthDay'] = substr($row['birthDay'], 0, 10);
        return $row;
    }
}
