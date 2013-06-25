<?php
namespace Magomogo\Persisted\Container;

use Test\DbFixture;
use Test\ObjectMother;
use Magomogo\Persisted\Container\Db;
use Test\CreditCard;
use Test\Person;
use Test\JobRecord;
use Test\Company;
use Test\Employee;

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
        $this->assertEquals(array(), $this->fixture->db->fetchAll("SELECT * FROM company_properties"));
        $this->assertEquals(array(), $this->fixture->db->fetchAll("SELECT * FROM person_properties"));
        $this->assertEquals(array(), $this->fixture->db->fetchAll("SELECT * FROM creditcard_properties"));
    }

    public function testSavesCreditCardIntoDatabase()
    {
        ObjectMother\CreditCard::datatransTesting()->putIn($this->sqliteContainer());

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
            $this->fixture->db->fetchAssoc("SELECT * FROM creditcard_properties")
        );
    }

    public function testSavesAPersonHavingCreditCardIntoDatabase()
    {
        ObjectMother\Person::maxim()->putIn($this->sqliteContainer());

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
            $this->fixture->db->fetchAssoc("SELECT * FROM person_properties")
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
            $this->fixture->db->fetchAssoc("SELECT * FROM creditcard_properties")
        );
    }

    public function testReadsModelFromTheDatabase()
    {
        $maximId = ObjectMother\Person::maxim()->putIn($this->sqliteContainer());
        $this->assertEquals(
            ObjectMother\Person::maxim($maximId)->politeTitle(),
            Person\Model::load($this->sqliteContainer(), $maximId)->politeTitle()
        );
    }

    public function testCanUpdateModelInTheDatabase()
    {
        $maximId = ObjectMother\Person::maxim()->putIn($this->sqliteContainer());
        $maxim = Person\Model::load($this->sqliteContainer(), $maximId);

        $maxim->phoneNumberIsChanged('903-903');
        $maxim->putIn($this->sqliteContainer());

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
            $this->fixture->db->fetchAssoc("SELECT * FROM employee_properties")
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
        $jobRecordProps = new JobRecord\Properties();
        $jobRecordProps->foreign()->currentCompany = new Company\Properties(array('name' => 'XIAG'));
        $jobRecordProps->foreign()->currentCompany->putIn($this->sqliteContainer());
        $jobRecordProps->foreign()->previousCompany = new Company\Properties(array('name' => 'NSTU'));
        $jobRecordProps->foreign()->previousCompany->putIn($this->sqliteContainer());

        $id = $jobRecordProps->putIn($this->sqliteContainer());

        $this->assertEquals(
            new JobRecord\Model(
                new Company\Model($jobRecordProps->foreign()->currentCompany),
                new Company\Model($jobRecordProps->foreign()->previousCompany),
                $jobRecordProps
            ),
            JobRecord\Model::load($this->sqliteContainer(), $id)
        );
    }

    public function testCreatesTwoRecordsOfSameType()
    {
        $this->persistTwoKeymarkers();
        $this->assertEquals('2', $this->fixture->db->fetchColumn('select count(1) from keymarker_properties'));
    }

    private function persistTwoKeymarkers()
    {
        $persistedKeymarker1 = ObjectMother\Keymarker::friend();
        $persistedKeymarker1->putIn($this->sqliteContainer());
        $persistedKeymarker2 = ObjectMother\Keymarker::IT();
        $persistedKeymarker2->putIn($this->sqliteContainer());
        return array($persistedKeymarker1, $persistedKeymarker2);
    }

    public function testStoresPersonKeymarkers()
    {
        list ($persistedKeymarker1, $persistedKeymarker2) = $this->persistTwoKeymarkers();

        $person = ObjectMother\Person::maxim();
        $person->tag($persistedKeymarker1);
        $person->tag($persistedKeymarker2);

        $id = $person->putIn($this->sqliteContainer());

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
        $id = $vova->putIn($this->sqliteContainer());

        $this->assertNull(
            $this->fixture->db->fetchColumn('SELECT lastName FROM person_properties WHERE id = ?', array($id))
        );

        $person = Person\Model::load($this->sqliteContainer(), $id);
        $this->assertNull($person->lastName());
    }

    public function testAModelCanBeDeletedFromContainer()
    {
        $cc = ObjectMother\CreditCard::datatransTesting();
        $id = $cc->putIn($this->sqliteContainer());

        $cc->deleteFrom($this->sqliteContainer());

        $this->setExpectedException('Magomogo\\Persisted\\Exception\\NotFound');
        CreditCard\Model::load($this->sqliteContainer(), $id);
    }

//----------------------------------------------------------------------------------------------------------------------

    private function putEmployeeIn($container)
    {
        $props = ObjectMother\Employee::maximProperties();
        $props->foreign()->company->putIn($container);
        $props->putIn($container);
        return new Employee\Model(new Company\Model($props->foreign()->company), $props);
    }

    private function sqliteContainer()
    {
        return new Db($this->fixture->db, 'Test\\');
    }
}
