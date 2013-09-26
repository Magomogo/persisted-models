<?php
namespace Magomogo\Persisted;

use Doctrine\CouchDB\CouchDBClient;
use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\Container\CouchDb;
use Magomogo\Persisted\Container\Memory;
use Magomogo\Persisted\Test\DbFixture;
use Magomogo\Persisted\Container\SqlDb;
use Magomogo\Persisted\Test\DbNames;
use Magomogo\Persisted\Test\Keymarker;
use Magomogo\Persisted\Test\ObjectMother;
use Magomogo\Persisted\Test\Company;
use Magomogo\Persisted\Test\Employee;
use Magomogo\Persisted\Test\JobRecord;

class ModelsPersistencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider modelsProvider
     *
     * @param callable|ModelInterface $model
     * @param callable|ContainerInterface $container
     */
    public function testCanBePutInAndLoadedFrom($model, $container)
    {
        if (is_callable($container)) {
            $container = call_user_func($container);
        }
        if (is_null($container)) {
            $this->markTestSkipped('Not configured');
        }
        if (is_callable($model)) {
            $model = call_user_func($model, $container);
        }

        $id = $model->save($container);
        $this->assertEquals($model, $model::load($container, $id));
        exit;
    }

    /**
     * @dataProvider modelsProvider
     *
     * @param callable|ModelInterface $model
     * @param callable|ContainerInterface $container
     */
    public function testAModelCanBeDeletedFromContainer($model, $container)
    {
        if (is_callable($container)) {
            $container = call_user_func($container);
        }
        if (is_null($container)) {
            $this->markTestSkipped('Not configured');
        }
        if (is_callable($model)) {
            $model = call_user_func($model, $container);
        }

        if (method_exists($model, 'deleteFrom')) {
            $id = $model->save($container);
            $model->deleteFrom($container);

            $this->setExpectedException('Magomogo\\Persisted\\Exception\\NotFound');
            $model::load($container, $id);
        }
    }

    public static function modelsProvider()
    {
        return array(
            array(array(__CLASS__, 'personHavingKeymarkers'), array(__CLASS__, 'couchDbContainer')),

            array(ObjectMother\CreditCard::datatransTesting(), new Memory()),
            array(ObjectMother\CreditCard::datatransTesting(), array(__CLASS__, 'sqliteContainer')),
            array(ObjectMother\CreditCard::datatransTesting(), array(__CLASS__, 'postgresContainer')),
            array(ObjectMother\CreditCard::datatransTesting(), array(__CLASS__, 'mysqlContainer')),
            array(ObjectMother\CreditCard::datatransTesting(), array(__CLASS__, 'couchDbContainer')),

            array(ObjectMother\Person::maxim(), new Memory()),
            array(ObjectMother\Person::maxim(), array(__CLASS__, 'sqliteContainer')),
            array(ObjectMother\Person::maxim(), array(__CLASS__, 'postgresContainer')),
            array(ObjectMother\Person::maxim(), array(__CLASS__, 'mysqlContainer')),
            array(ObjectMother\Person::maxim(), array(__CLASS__, 'couchDbContainer')),

            array(ObjectMother\Person::maximWithoutCC(), new Memory()),
            array(ObjectMother\Person::maximWithoutCC(), array(__CLASS__, 'sqliteContainer')),
            array(ObjectMother\Person::maximWithoutCC(), array(__CLASS__, 'postgresContainer')),
            array(ObjectMother\Person::maximWithoutCC(), array(__CLASS__, 'mysqlContainer')),
            array(ObjectMother\Person::maximWithoutCC(), array(__CLASS__, 'couchDbContainer')),

            array(ObjectMother\Company::xiag(), new Memory()),
            array(ObjectMother\Company::xiag(), array(__CLASS__, 'sqliteContainer')),
            array(ObjectMother\Company::xiag(), array(__CLASS__, 'postgresContainer')),
            array(ObjectMother\Company::xiag(), array(__CLASS__, 'mysqlContainer')),
            array(ObjectMother\Company::xiag(), array(__CLASS__, 'couchDbContainer')),

            array(ObjectMother\Keymarker::friend(), new Memory()),
            array(ObjectMother\Keymarker::friend(), array(__CLASS__, 'sqliteContainer')),
            array(ObjectMother\Keymarker::friend(), array(__CLASS__, 'postgresContainer')),
            array(ObjectMother\Keymarker::friend(), array(__CLASS__, 'mysqlContainer')),
            array(ObjectMother\Keymarker::friend(), array(__CLASS__, 'couchDbContainer')),

            array(array(__CLASS__, 'employeeModel'), new Memory()),
            array(array(__CLASS__, 'employeeModel'), array(__CLASS__, 'sqliteContainer')),
            array(array(__CLASS__, 'employeeModel'), array(__CLASS__, 'postgresContainer')),
            array(array(__CLASS__, 'employeeModel'), array(__CLASS__, 'mysqlContainer')),

            array(array(__CLASS__, 'jobRecord'), new Memory()),
            array(array(__CLASS__, 'jobRecord'), array(__CLASS__, 'sqliteContainer')),
            array(array(__CLASS__, 'jobRecord'), array(__CLASS__, 'postgresContainer')),
            array(array(__CLASS__, 'jobRecord'), array(__CLASS__, 'mysqlContainer')),
            array(array(__CLASS__, 'jobRecord'), array(__CLASS__, 'couchDbContainer')),

            array(array(__CLASS__, 'personHavingKeymarkers'), new Memory()),
            array(array(__CLASS__, 'personHavingKeymarkers'), array(__CLASS__, 'sqliteContainer')),
            array(array(__CLASS__, 'personHavingKeymarkers'), array(__CLASS__, 'postgresContainer')),
            array(array(__CLASS__, 'personHavingKeymarkers'), array(__CLASS__, 'mysqlContainer')),

        );
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function sqliteContainer()
    {
        return new SqlDb(DbFixture::inMemory()->install()->db, new DbNames);
    }

    private static function postgresContainer()
    {
        return new SqlDb(DbFixture::inPostgres()->install()->db, new DbNames);
    }

    private static function mysqlContainer()
    {
        return new SqlDb(DbFixture::inMysql()->install()->db, new DbNames);
    }

    private static function couchDbContainer()
    {
        $client = CouchDBClient::create(
            array(
                'dbname' => 'test_persisted_models'
            )
        );
        $client->deleteDatabase('test_persisted_models');
        $client->createDatabase('test_persisted_models');

        return new CouchDb($client);
    }

    /**
     * @param ContainerInterface $container
     * @return Employee\Model
     */
    private static function employeeModel($container)
    {
        $company = ObjectMother\Company::xiag();
        $company->save($container);

        $keymarker = new Keymarker\Model(new Keymarker\Properties(array('id' => 'test')));
        $keymarker->save($container);

        return new Employee\Model(
            $company,
            new Employee\Properties(array('firstName' => 'John')),
            array($keymarker)
        );
    }

    private static function jobRecord($container)
    {
        $company1 = ObjectMother\Company::xiag();
        $company1->save($container);

        $company2 = ObjectMother\Company::nstu();
        $company2->save($container);

        return new JobRecord\Model($company1, $company2);
    }

    private static function personHavingKeymarkers($container)
    {
        $friend = ObjectMother\Keymarker::friend();
        $friend->save($container);
        $IT = ObjectMother\Keymarker::IT();
        $IT->save($container);

        $person = ObjectMother\Person::maxim();
        $person->tag($friend);
        $person->tag($IT);

        return $person;
    }
}
