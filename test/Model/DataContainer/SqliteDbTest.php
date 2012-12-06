<?php
namespace Model\DataContainer;
use Test\DbFixture;
use Test\ObjectMother\CreditCard;
use Test\ObjectMother\Person;
use Model\DataContainer\Db;
use Model\ContainerReadyInterface;

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
        $this->assertEquals(array(), $this->fixture->db->fetchAll("SELECT * FROM person_properties"));
        $this->assertEquals(array(), $this->fixture->db->fetchAll("SELECT * FROM creditCard_properties"));
    }

    public function testSavesCreditCardIntoDatabase()
    {
        $cc = CreditCard::datatransTesting();
        $cc->putIn($this->containerFor($cc));

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
        $person = Person::maxim();
        $person->putIn($this->containerFor($person));

        $this->assertEquals(
            array(
                'id' => '1',
                'title' => 'Mr.',
                'firstName' => 'Maxim',
                'lastName' => 'Gnatenko',
                'email' => 'maxim@xiag.ch',
                'phone' => '+7923-117-2801',
                'creditCard' => '1'
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
        $maximId = $this->putMaximUntoContainer();

        $this->assertEquals(Person::maxim($maximId), $this->loadPersonFromContainer($maximId));
    }

    public function testCanUpdateModelInTheDatabase()
    {
        $maximId = $this->putMaximUntoContainer();
        $maxim = $this->loadPersonFromContainer($maximId);

        $maxim->phoneNumberIsChanged('903-903');
        $maxim->putIn($this->containerFor($maxim));

        $this->assertContains('903-903', $this->loadPersonFromContainer($maximId)->contactInfo());
    }

//----------------------------------------------------------------------------------------------------------------------

    private function containerFor(ContainerReadyInterface $model)
    {
        return new Db($this->fixture->db);
    }

    private function putMaximUntoContainer()
    {
        $person = Person::maxim();
        return $person->putIn($this->containerFor($person));
    }

    private function loadPersonFromContainer($id)
    {
        $container = new Db($this->fixture->db);
        return new \Person\Model(
            $container->loadProperties(new \Person\Properties($id))
        );
    }

}
