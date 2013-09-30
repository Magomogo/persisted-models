<?php

namespace Magomogo\Persisted\Container;

use Mockery as m;
use Magomogo\Persisted\Test\ObjectMother;
use Magomogo\Persisted\Test\Company;
use Magomogo\Persisted\Test\Keymarker;

class CouchDbTest extends \PHPUnit_Framework_TestCase
{
    public function testPostsNewProperties()
    {
        $client = m::mock();
        $client->shouldReceive('findDocument');
        $client->shouldReceive('postDocument')
            ->with(m::subset(array('name' => 'XIAG')))
            ->once()
            ->andReturn(array('id-hash-FE2343', '1-rev-hash-35AC'));

        $id = ObjectMother\Company::xiag()->save(self::container($client));

        $this->assertSame('id-hash-FE2343', $id);
    }

    public function testPutsExistingProperties()
    {
        $client = self::couchDbHavingDoc();
        $client->shouldReceive('putDocument')
            ->with(m::subset(array('name' => 'XIAG')), 'id-hash-43FF')
            ->once();

        $props = new Company\Properties(array('name' => 'XIAG'));
        $props->persisted('id-hash-43FF', self::container());

        $company = new Company\Model($props);

        $company->save(self::container($client));
    }

    public function testLoadsProperties()
    {
        $client = m::mock();
        $client->shouldReceive('findDocument')
            ->with('id-hash-FE2343')
            ->andReturnUsing(function() {
                $resp = new \stdClass();
                $resp->status = 200;
                $resp->body = array(
                    'name' => 'A company'
                );
                return $resp;
            })
            ->once();

        $this->assertEquals('A company', Company\Model::load(self::container($client), 'id-hash-FE2343')->name());
    }

    public function testReportsNotExistingProperties()
    {
        $this->setExpectedException('Magomogo\\Persisted\\Exception\\NotFound');
        Company\Model::load(self::container(), 'id-hash-FE2343');
    }

    public function testDeletesProperties()
    {
        $client = self::couchDbHavingDoc();
        $client->shouldReceive('deleteDocument')->with('id-hash-43FF', '1-rev-hash-45323DD');
        ObjectMother\CreditCard::datatransTesting()->deleteFrom(self::container($client));
    }

    public function testConvertsDateTimeObjectBeforeSavingToCouchDb()
    {
        $client = self::couchDbHavingDoc();
        $client->shouldReceive('postDocument')
            ->with(m::subset(array('created' => '2012-12-08T10:36:00+07:00')))
            ->once();

        ObjectMother\Keymarker::IT()->save(self::container($client));
    }

    public function testConvertsDateStringToObjectAfterLoadingFromCouchDb()
    {
        $client = self::couchDbHavingDoc(
            array(
                'created' => '2012-12-08T10:36:00+07:00'
            )
        );

        $properties = new Keymarker\Properties();
        $properties->loadFrom(self::container($client), 'id');

        $this->assertInstanceOf('DateTime', $properties->created);
        $this->assertEquals(new \DateTime('2012-12-08T10:36:00+07:00'), $properties->created);
    }

    public function testSavesDocumentHavingNaturalKey()
    {
        $client = m::mock();
        $client->shouldReceive('findDocument');
        $client->shouldReceive('postDocument')
            ->with(m::subset(array('name' => 'XIAG')))
            ->once()
            ->andReturn(array('id-hash-FE2343', '1-rev-hash-35AC'));

        $id = ObjectMother\Company::xiag()->save(self::container($client));

    }

//----------------------------------------------------------------------------------------------------------------------

    private static function container($client = null)
    {
        return new CouchDb($client ?: self::emptyCouchDb());
    }

    /**
     * @return m\MockInterface
     */
    private static function emptyCouchDb()
    {
        return m::mock(
            'counch db client',
            function ($mock) {
                $mock->shouldReceive('findDocument')
                    ->andReturnUsing(
                        function () {
                            $resp = new \stdClass();
                            $resp->status = 404;
                            return $resp;
                        }
                    );
            }
        );
    }

    /**
     * @return m\MockInterface
     */
    private static function couchDbHavingDoc($doc = array())
    {
        return m::mock(
            'counch db client',
            function ($mock) use ($doc) {
                $mock->shouldReceive('findDocument')
                    ->andReturnUsing(
                        function () use ($doc) {
                            $resp = new \stdClass();
                            $resp->status = 200;
                            $resp->body =
                                array_merge(
                                    array('_id' => 'id-hash-43FF', '_rev' => '1-rev-hash-45323DD'),
                                    $doc
                                );
                            return $resp;
                        }
                    );
            }
        );
    }

}
