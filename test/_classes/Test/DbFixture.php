<?php
namespace Test;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;

class DbFixture
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    public $db;

    public function __construct()
    {
        $this->db = DriverManager::getConnection(
            array(
                'memory' => true,
                'user' => '',
                'password' => '',
                'driver' => 'pdo_sqlite',
            ),
            new Configuration
        );
    }

    public function install()
    {
        self::installSchema($this->db);
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function installSchema(Connection $db)
    {
        $db->exec(<<<SQL

CREATE TABLE company_properties (
  id INTEGER CONSTRAINT pk_company PRIMARY KEY AUTOINCREMENT,
  name TEXT
);

CREATE TABLE jobrecord_properties (
  id INTEGER CONSTRAINT pk_jobrecord PRIMARY KEY AUTOINCREMENT,
  currentCompany INTEGER CONSTRAINT fk_jobrecord_to_company1 REFERENCES company_properties (id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  previousCompany INTEGER CONSTRAINT fk_jobrecord_to_company2 REFERENCES company_properties (id)
    ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE table keymarker_properties (
  id TEXT CONSTRAINT pk_keymarker PRIMARY KEY,
  created DATE
);

CREATE table creditcard_properties (
  id INTEGER CONSTRAINT pk_creditcard PRIMARY KEY AUTOINCREMENT,
  system TEXT,
  pan TEXT,
  validMonth TEXT,
  validYear TEXT,
  ccv TEXT,
  cardholderName TEXT
);

CREATE TABLE person_properties (
  id INTEGER CONSTRAINT pk_person PRIMARY KEY AUTOINCREMENT,
  title TEXT,
  firstName TEXT,
  lastName TEXT,
  email TEXT,
  phone TEXT,
  birthDay DATE,
  creditCard INTEGER CONSTRAINT fk_person_to_cc REFERENCES creditcard_properties (id)
    ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE employee_properties (
  id INTEGER CONSTRAINT pk_person PRIMARY KEY AUTOINCREMENT,
  title TEXT,
  firstName TEXT,
  lastName TEXT,
  email TEXT,
  phone TEXT,
  birthDay DATE,
  creditCard INTEGER CONSTRAINT fk_person_to_cc REFERENCES creditcard_properties (id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  company INTEGER CONSTRAINT fk_person_to_company REFERENCES company_properties (id)
    ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE person2keymarker (
  person_properties INTEGER CONSTRAINT fk_person2keymarker1 REFERENCES person_properties (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  keymarker_properties INTEGER CONSTRAINT fk_person2keymarker2 REFERENCES keymarker_properties (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT pk_person_2_keymarker PRIMARY KEY (person_properties, keymarker_properties)
);
SQL
        );
    }
}
