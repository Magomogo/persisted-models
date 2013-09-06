<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\Test\DbFixture;
use Magomogo\Persisted\Test\DbNames;
use Magomogo\Persisted\Test\ObjectMother;
use Magomogo\Persisted\Container\SqlDb;
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
        $this->fixture = DbFixture::inMemory();
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

        $this->assertEquals(
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

        $this->assertEquals(
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

        $this->assertEquals(
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

    public function testWorksWithNulls()
    {
        $personProperties = new Person\Properties();
        $personProperties->firstName = 'Vova';
        $personProperties->lastName = null;

        $vova = new Person\Model($personProperties);
        $id = $vova->save($this->sqliteContainer());

        $this->assertNull(
            $this->fixture->db->fetchColumn("SELECT lastName FROM person WHERE id = ?", array($id))
        );

        $person = Person\Model::load($this->sqliteContainer(), $id);
        $this->assertNull($person->lastName());
    }

//----------------------------------------------------------------------------------------------------------------------

    private function putEmployeeIn($container)
    {
        $company = ObjectMother\Company::xiag();
        $company->save($container);

        $employee = ObjectMother\Employee::maxim($company);
        $employee->save($container);
        return $employee;
    }

    private function sqliteContainer()
    {
        return new SqlDb($this->fixture->db, new DbNames);
    }

    private static function smoothBirthDayFormatDifference($row)
    {
        $row['birthDay'] = substr($row['birthDay'], 0, 10);
        return $row;
    }
}
