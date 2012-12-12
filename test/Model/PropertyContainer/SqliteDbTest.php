<?php
namespace Magomogo\Model\PropertyContainer;
use Test\DbFixture;
use Test\ObjectMother\CreditCard;
use Test\ObjectMother\Person;
use Test\ObjectMother\Company;
use Magomogo\Model\PropertyContainer\Db;
use JobRecord;
use Test\ObjectMother\Keymarker;

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
                'company' => null,
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
            $this->fixture->db->fetchAssoc("SELECT * FROM person_properties")
        );
    }

    public function testReadsEmployeeModel()
    {
        $employee = $this->putEmployeeIn($this->sqliteContainer());
        $newEmployee = \Employee\Model::loadFrom($this->sqliteContainer(), 1);
        $this->assertEquals($employee, $newEmployee);
    }

    public function testCanSaveAndLoadAJobRecord()
    {
        $currentCompany = Company::xiag();
        $currentCompany->putIn(self::sqliteContainer());
        $previousCompany = Company::nstu();
        $previousCompany->putIn(self::sqliteContainer());

        $record = new JobRecord\Model($currentCompany, $previousCompany, new \JobRecord\Properties());
        $id = $record->putIn(self::sqliteContainer());

        $this->assertEquals($record, $record::loadFrom(self::sqliteContainer(), $id));
    }

    public function testStoresPersonKeymarkers()
    {
        $persistedKeymarker1 = Keymarker::friend();
        $persistedKeymarker1->putIn($this->sqliteContainer());
        $persistedKeymarker2 = Keymarker::IT();
        $persistedKeymarker2->putIn($this->sqliteContainer());

        $person = Person::maxim();
        $person->tag($persistedKeymarker1);
        $person->tag($persistedKeymarker2);

        $id = $person->putIn($this->sqliteContainer());

        $this->assertEquals(
            $person,
            $person::loadFrom($this->sqliteContainer(), $id)
        );
    }

//----------------------------------------------------------------------------------------------------------------------

    private function putEmployeeIn($container)
    {
        $company = Company::xiag();
        $company->putIn($container);

        $employee = new \Employee\Model($company, Person::maximProperties());
        $employee->putIn($container);
        return $employee;
    }

    private function sqliteContainer()
    {
        return new Db($this->fixture->db);
    }

    private function loadPersonFromContainer($id)
    {
        return \Person\Model::loadFrom(self::sqliteContainer(), $id);
    }
}
