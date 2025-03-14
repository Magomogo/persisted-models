<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\ModelInterface;
use Test\DbFixture;
use Test\ObjectMother;
use Magomogo\Persisted\Container\Db;
use Test\CreditCard;
use Test\Person;
use Test\JobRecord;
use Test\Company;
use Test\Employee;
use PHPUnit\Framework\TestCase;

class SqliteDbTest extends TestCase
{
    /**
     * @var DbFixture
     */
    private $fixture;

    protected function setUp(): void
    {
        $this->fixture = new DbFixture();
        $this->fixture->install();
    }

    public function testFixtureHasCorrectTablesCreated()
    {
        $this->assertEquals(array(), $this->fixture->db->fetchAllAssociative("SELECT * FROM company_properties"));
        $this->assertEquals(array(), $this->fixture->db->fetchAllAssociative("SELECT * FROM person_properties"));
        $this->assertEquals(array(), $this->fixture->db->fetchAllAssociative("SELECT * FROM creditcard_properties"));
    }

    public function testSavesCreditCardIntoDatabase()
    {
        ObjectMother\CreditCard::datatransTesting()->save($this->sqliteContainer());

        $this->assertEquals(
            array(
                'id' => '1',
                'system' => 'VISA',
                'pan' => '9500000000000001',
                'validMonth' => '12',
                'validYear' => '2015',
                'ccv' => '234',
                'cardholderName' => 'Maxim Gnatenko'
            ),
            $this->fixture->db->fetchAssociative("SELECT * FROM creditcard_properties")
        );
    }

    public function testSavesAPersonHavingCreditCardIntoDatabase()
    {
        ObjectMother\Person::maxim()->save($this->sqliteContainer());

        $this->assertEquals(
            array(
                'id' => '1',
                'title' => 'Mr.',
                'firstName' => 'Maxim',
                'lastName' => 'Gnatenko',
                'email' => 'maxim@xiag.ch',
                'phone' => '+7923-117-2801',
                'creditCard' => '1',
                'birthDay' => '1975-07-07T00:00:00+07:00'
            ),
            $this->fixture->db->fetchAssociative("SELECT * FROM person_properties")
        );

        $this->assertEquals(
            array(
                'id' => '1',
                'system' => 'VISA',
                'pan' => '9500000000000001',
                'validMonth' => '12',
                'validYear' => '2015',
                'ccv' => '234',
                'cardholderName' => 'Maxim Gnatenko'
            ),
            $this->fixture->db->fetchAssociative("SELECT * FROM creditcard_properties")
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

        $this->assertEquals(
            array(
                'id' => '1',
                'title' => 'Mr.',
                'firstName' => 'Maxim',
                'lastName' => 'Gnatenko',
                'email' => 'maxim@xiag.ch',
                'phone' => '+7923-117-2801',
                'creditCard' => '1',
                'company' => '1',
                'birthDay' => '1975-07-07T00:00:00+07:00'
            ),
            $this->fixture->db->fetchAssociative("SELECT * FROM employee_properties")
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
        $this->assertEquals('2', $this->fixture->db->fetchOne('select count(1) from keymarker_properties'));
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

        $this->assertNull(
            $this->fixture->db->fetchOne('SELECT lastName FROM person_properties WHERE id = ?', array($id))
        );

        $person = Person\Model::load($this->sqliteContainer(), $id);
        $this->assertNull($person->lastName());
    }

    public function testAModelCanBeDeletedFromContainer()
    {
        $cc = ObjectMother\CreditCard::datatransTesting();
        $id = $cc->save($this->sqliteContainer());

        $cc->deleteFrom($this->sqliteContainer());

        $this->expectException('Magomogo\\Persisted\\Exception\\NotFound');
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
        return new Db($this->fixture->db, 'Test\\');
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
}
