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
                'company' => null
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
        $this->markTestIncomplete();
        
        $company = Company::xiag();
        $company->putIn($this->sqliteContainer());

        $employee = new \Employee\Model($company, Person::maximProperties());
        $employee->putIn($this->sqliteContainer());

        $this->assertEquals(
            array(
                'id' => '1',
                'title' => 'Mr.',
                'firstName' => 'Maxim',
                'lastName' => 'Gnatenko',
                'email' => 'maxim@xiag.ch',
                'phone' => '+7923-117-2801',
                'creditCard' => '1',
                'company' => '1'
            ),
            $this->fixture->db->fetchAssoc("SELECT * FROM person_properties")
        );
    }

//----------------------------------------------------------------------------------------------------------------------

    private function sqliteContainer()
    {
        return new Db($this->fixture->db);
    }

    private function loadPersonFromContainer($id)
    {
        return new \Person\Model(self::sqliteContainer()->loadProperties(new \Person\Properties($id)));
    }

}
