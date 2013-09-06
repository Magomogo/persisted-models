<?php

namespace Magomogo\Persisted\Container\Db;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Mockery as m;
use Magomogo\Persisted\Test\DbNames;
use Magomogo\Persisted\Test\ObjectMother\Keymarker;
use Magomogo\Persisted\Test\ObjectMother\Person;

class SchemaCreatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    private $connection;

    protected function setUp()
    {
        $this->connection = DriverManager::getConnection(
            array(
                'memory' => true,
                'user' => '',
                'password' => '',
                'driver' => 'pdo_sqlite',
            ),
            new Configuration
        );
    }

    public function testCreatesATableForPersonProperties()
    {
        self::schema($this->connection->getSchemaManager())->schemaFor(Person::maxim());

        $this->assertSame(
            self::asOneLine(
            <<<SQL
CREATE TABLE "person" (
  id INTEGER NOT NULL,
  "title" CLOB DEFAULT NULL,
  "firstName" CLOB DEFAULT NULL,
  "lastName" CLOB DEFAULT NULL,
  "phone" CLOB DEFAULT NULL,
  "email" CLOB DEFAULT NULL,
  "creditCard" INTEGER DEFAULT NULL,
  "birthDay" DATETIME DEFAULT NULL,
  PRIMARY KEY(id)
)
SQL
            ),
            $this->connection->fetchColumn("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = 'person'")
        );

        $this->assertRegExp(
            '/CREATE INDEX .+ ON "person" \\("creditCard"\\)/',
            $this->connection->fetchColumn("SELECT sql FROM sqlite_master WHERE type = 'index' AND tbl_name = 'person'")
        );

    }

    public function testATableForAggregatedProperties()
    {
        self::schema($this->connection->getSchemaManager())->schemaFor(Person::maxim());

        $this->assertSame(
            self::asOneLine(
                <<<SQL
CREATE TABLE "creditcard" (
  id INTEGER NOT NULL,
  "system" CLOB DEFAULT NULL,
  "pan" CLOB DEFAULT NULL,
  "validMonth" CLOB DEFAULT NULL,
  "validYear" CLOB DEFAULT NULL,
  "ccv" CLOB DEFAULT NULL,
  "cardholderName" CLOB DEFAULT NULL,
  PRIMARY KEY(id)
)
SQL
            ),
            $this->connection->fetchColumn("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = 'creditcard'")
        );
    }

    public function testCreatesATableForManyToManyReference()
    {
        $taggedPerson = Person::maxim();
        $taggedPerson->tag(Keymarker::IT());

        self::schema($this->connection->getSchemaManager())->schemaFor($taggedPerson);

        $this->assertSame(
            'CREATE TABLE "person2keymarker" ("person" INTEGER DEFAULT NULL, "keymarker" VARCHAR(255) DEFAULT NULL)',
            $this->connection->fetchColumn("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = 'person2keymarker'")
        );

        $this->assertRegExp(
            '/CREATE INDEX .+ ON "person2keymarker" \\("person"\\)/',
            $this->connection->fetchColumn("SELECT sql FROM sqlite_master WHERE type = 'index' AND tbl_name = 'person2keymarker'")
        );
    }

    private static function schema($manager)
    {
        return new SchemaCreator(
            $manager,
            new DbNames()
        );
    }

    private static function asOneLine($sql)
    {
        return preg_replace(array('/\s+/m', '/\( /', '/ \)/'), array(' ', '(', ')'), $sql);
    }
}
