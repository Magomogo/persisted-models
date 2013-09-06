<?php
namespace Magomogo\Persisted\Test;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;
use Magomogo\Persisted\Container\SqlDb\SchemaCreator;
use Magomogo\Persisted\Test\JobRecord\Model;
use Magomogo\Persisted\Test\ObjectMother;

class DbFixture
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    public $db;

    /**
     * @return self
     */
    public static function inMemory()
    {
        return new self(
            DriverManager::getConnection(
                array(
                    'memory' => true,
                    'user' => '',
                    'password' => '',
                    'driver' => 'pdo_sqlite',
                ),
                new Configuration
            )
        );
    }

    /**
     * @return self
     */
    public static function inPostgres()
    {
        return new self(self::postgresDb());
    }

    /**
     * @return self
     */
    public static function inMysql()
    {
        return new self(self::mysqlDb());
    }

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @return $this
     */
    public function install()
    {
        $platformSpecificCleanUp = array(
            'postgresql' => <<<SQL
DROP TABLE IF EXISTS creditcard, person, company, jobrecord, keymarker, person2keymarker CASCADE
SQL
            ,
            'mysql' => <<<SQL
DELETE FROM keymarker;
DROP TABLE IF EXISTS creditcard, person, company, jobrecord, keymarker, person2keymarker;
SQL

        );

        if (array_key_exists($this->db->getDatabasePlatform()->getName(), $platformSpecificCleanUp)) {
            $this->db->exec($platformSpecificCleanUp[$this->db->getDatabasePlatform()->getName()]);
        }

        self::installSchema($this->db);
        return $this;
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
    private static function postgresDb()
    {
        $conn = DriverManager::getConnection(
            file_exists(__DIR__ . '/pgsql.conf.php') ? include __DIR__ . '/pgsql.conf.php' : self::travisCiPostgres(),
            new Configuration
        );
        $conn->exec("SET TIME ZONE '+7'");
        return $conn;
    }

    /**
     * @return Connection
     */
    private static function mysqlDb()
    {
        $conn = DriverManager::getConnection(
            file_exists(__DIR__ . '/mysql.conf.php') ? include __DIR__ . '/mysql.conf.php' : self::travisCiMysql(),
            new Configuration
        );
        $conn->exec('SET time_zone = \'+07:00\'');
        return $conn;
    }

    private static function travisCiMysql()
    {
        return array(
            'user' => 'travis',
            'password' => '',
            'driver' => 'pdo_mysql',
            'host' => '127.0.0.1',
            'dbname' => 'magomogo_persisted_models'
        );
    }

    private static function travisCiPostgres()
    {
        return array(
            'user' => 'postgres',
            'password' => '',
            'driver' => 'pdo_pgsql',
            'host' => '127.0.0.1',
            'dbname' => 'magomogo_persisted_models'
        );
    }
}
