<?php
namespace Magomogo\Persisted\Test;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;
use Magomogo\Persisted\Container\Db\SchemaCreator;
use Magomogo\Persisted\Test\JobRecord\Model;
use Magomogo\Persisted\Test\ObjectMother;

class DbFixture
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    public $db;

    public function __construct()
    {
        $this->db = self::memoryDb();
        //$this->db = self::postgresDb();
        //$this->db = self::mysqlDb();
    }

    public function install()
    {
        if ($this->db->getDatabasePlatform()->getName() !== 'sqlite') {
            $this->db->exec(
                <<<SQL
DROP TABLE IF EXISTS creditcard, person, company, employee, jobrecord, keymarker, person2keymarker CASCADE
SQL
            );
        }

        self::installSchema($this->db);
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function installSchema(Connection $db)
    {
        $creator = new SchemaCreator($db->getSchemaManager(), new DbNames());
        $creator->schemaFor(ObjectMother\Company::xiag());
        $creator->schemaFor(new JobRecord\Model(ObjectMother\Company::xiag(), ObjectMother\Company::nstu()));
        $creator->schemaFor(ObjectMother\Keymarker::IT());
        $creator->schemaFor(ObjectMother\CreditCard::datatransTesting());

        $taggedEmployee = ObjectMother\Employee::maxim();
        $taggedEmployee->tag(ObjectMother\Keymarker::IT());
        $creator->schemaFor($taggedEmployee);
    }

    /**
     * @return Connection
     */
    private static function memoryDb()
    {
        return DriverManager::getConnection(
            array(
                'memory' => true,
                'user' => '',
                'password' => '',
                'driver' => 'pdo_sqlite',
            ),
            new Configuration
        );
    }

    /**
     * @return Connection
     */
    private static function postgresDb()
    {
        if (file_exists(__DIR__ . '/pgsql.conf.php')) {
            $conn = DriverManager::getConnection(include __DIR__ . '/pgsql.conf.php', new Configuration);
            $conn->exec("SET TIME ZONE '+7'");
            return $conn;
        }

        return null;
    }

    /**
     * @return Connection
     */
    private static function mysqlDb()
    {
        if (file_exists(__DIR__ . '/mysql.conf.php')) {
            $conn = DriverManager::getConnection(include __DIR__ . '/mysql.conf.php', new Configuration);
            return $conn;
        }

        return null;
    }

}
