<?php
namespace Model\DataContainer;
use Test\DbFixture;
use Test\ObjectMother\CreditCard;
use Test\ObjectMother\Person;
use Test\ObjectMother\Company;
use Model\DataContainer\Db;

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
                'company_id' => null
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

        $this->assertEquals(Person::maxim($maximId), $this->loadPersonFromContainer($maximId));
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
                'company_id' => '1'
            ),
            $this->fixture->db->fetchAssoc("SELECT * FROM person_properties")
        );
    }

    public function testReadsEmployeeModel()
    {
        /** @var \Company\Model $company */
        /** @var \Employee\Model $employee */
        list($company, $employee) = $this->putEmployeeIn($this->sqliteContainer());

        $loadedEmployee = $company->getEmployeeById($employee->id(), $this->fixture->db);

        $this->assertEquals($employee, $loadedEmployee);
    }

//----------------------------------------------------------------------------------------------------------------------

    private function putEmployeeIn($container)
    {
        $company = Company::xiag();
        $company->putIn($container);

        $person = Person::maxim();
        $person->putIn($container);

        $employee = $person->hiredBy($company, $this->fixture->db);

        return array($company, $employee);
    }

    private function sqliteContainer()
    {
        return new Db($this->fixture->db);
    }

    private function loadPersonFromContainer($id)
    {
        return new \Person\Model(self::sqliteContainer()->loadProperties(new \Person\Properties($id)));
    }

}
