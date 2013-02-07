<?php
namespace Magomogo\Model\PropertyContainer;
use Test\DbFixture;
use Test\ObjectMother\CreditCard;
use Test\ObjectMother\Person;
use Test\ObjectMother\Company;
use Magomogo\Model\PropertyContainer\Db;
use JobRecord;
use Test\ObjectMother\Keymarker;
use Test\ObjectMother\Employee;
use Company\Model as CompanyModel;
use Employee\Model as EmployeeModel;

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
        $this->assertEquals(array(), $this->fixture->db->fetchAll("SELECT * FROM creditCard_properties"));
    }

    public function testSavesCreditCardIntoDatabase()
    {
        CreditCard::datatransTesting()->putIn($this->sqliteContainer());

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
            $this->fixture->db->fetchAssoc("SELECT * FROM creditCard_properties")
        );
    }

    public function testSavesAPersonHavingCreditCardIntoDatabase()
    {
        Person::maxim()->putIn($this->sqliteContainer());

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
            $this->fixture->db->fetchAssoc("SELECT * FROM creditCard_properties")
        );
    }

    public function testReadsModelFromTheDatabase()
    {
        $maximId = Person::maxim()->putIn($this->sqliteContainer());
        $this->assertEquals(
            Person::maxim($maximId)->politeTitle(),
            $this->loadPersonFromContainer($maximId)->politeTitle()
        );
    }

    public function testCanUpdateModelInTheDatabase()
    {
        $maximId = Person::maxim()->putIn($this->sqliteContainer());
        $maxim = $this->loadPersonFromContainer($maximId);

        $maxim->phoneNumberIsChanged('903-903');
        $maxim->putIn($this->sqliteContainer());

        $this->assertContains('903-903', $this->loadPersonFromContainer($maximId)->contactInfo());
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
        $this->assertEquals($employee, $employee->newFrom($this->sqliteContainer(), 1));
    }

    public function testCanSaveAndLoadAJobRecord()
    {
        $currentCompany = Company::xiag();
        $currentCompany->putIn($this->sqliteContainer());
        $previousCompany = Company::nstu();
        $previousCompany->putIn($this->sqliteContainer());

        $jobRecordProps = new \JobRecord\Properties(
            null,
            array(
                'currentCompany' => $currentCompany->propertiesFrom($this->sqliteContainer()),
                'previousCompany' => $previousCompany->propertiesFrom($this->sqliteContainer())
            )
        );

        $record = new JobRecord\Model(
            $jobRecordProps->reference('currentCompany'),
            $jobRecordProps->reference('previousCompany'),
            $jobRecordProps
        );
        $id = $record->putIn($this->sqliteContainer());

        $this->assertEquals($record, $record->newFrom(self::sqliteContainer(), $id));
    }

    public function testCreatesTwoRecordsOfSameType()
    {
        $this->persistTwoKeymarkers();
        $this->assertEquals('2', $this->fixture->db->fetchColumn('select count(1) from keymarker_properties'));
    }

    private function persistTwoKeymarkers()
    {
        $persistedKeymarker1 = Keymarker::friend();
        $persistedKeymarker1->putIn($this->sqliteContainer());
        $persistedKeymarker2 = Keymarker::IT();
        $persistedKeymarker2->putIn($this->sqliteContainer());
        return array($persistedKeymarker1, $persistedKeymarker2);
    }

    public function testStoresPersonKeymarkers()
    {
        list ($persistedKeymarker1, $persistedKeymarker2) = $this->persistTwoKeymarkers();

        $person = Person::maxim();
        $person->tag($persistedKeymarker1);
        $person->tag($persistedKeymarker2);

        $id = $person->putIn($this->sqliteContainer());

        $this->assertEquals(
            $person,
            $person->newFrom($this->sqliteContainer(), $id)
        );
    }

    public function testWorksWithNulls()
    {
        $personProperties = new \Person\Properties();
        $personProperties->firstName = 'Vova';
        $personProperties->lastName = null;

        $vova = new \Person\Model($personProperties);
        $id = $vova->putIn(self::sqliteContainer());

        $this->assertNull(
            $this->fixture->db->fetchColumn('SELECT lastName FROM person_properties WHERE id = ?', array($id))
        );

        $properties = \Person\Properties::loadFrom(self::sqliteContainer(), $id);
        $this->assertNull($properties->lastName);
    }

    public function testACompanyCanBeDeletedFromContainer()
    {
        $company = Company::xiag();
        $companyId = $company->putIn($this->sqliteContainer());

        $company->deleteFrom($this->sqliteContainer());

        $this->setExpectedException('Magomogo\\Model\\Exception\\NotFound');
        $company->newFrom($this->sqliteContainer(), $companyId);
    }

//----------------------------------------------------------------------------------------------------------------------

    private function putEmployeeIn($container)
    {
        $properties = Employee::maximProperties();
        $company = new CompanyModel($properties->reference('company'));
        $company->putIn($container);

        $employee = new EmployeeModel($company, $properties);
        $employee->putIn($container);
        return $employee;
    }

    private function sqliteContainer()
    {
        return new Db($this->fixture->db);
    }

    private function loadPersonFromContainer($id)
    {
        $person = new \Person\Model(new \Person\Properties());
        return $person->newFrom($this->sqliteContainer(), $id);
    }
}
