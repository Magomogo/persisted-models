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
            new \Doctrine\DBAL\Configuration
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

CREATE TABLE company (
  id INTEGER CONSTRAINT pk_company PRIMARY KEY AUTOINCREMENT,
  name TEXT
);

CREATE TABLE job_record (
  id INTEGER CONSTRAINT pk_jobrecord PRIMARY KEY AUTOINCREMENT,
  currentCompany INTEGER CONSTRAINT fk_jobrecord_to_company1 REFERENCES company (id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  previousCompany INTEGER CONSTRAINT fk_jobrecord_to_company2 REFERENCES company (id)
    ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE table keymarker (
  id TEXT CONSTRAINT pk_keymarker PRIMARY KEY,
  created DATE
);

CREATE table credit_card (
  id INTEGER CONSTRAINT pk_creditcard PRIMARY KEY AUTOINCREMENT,
  system TEXT,
  pan TEXT,
  validMonth TEXT,
  validYear TEXT,
  ccv TEXT,
  cardholderName TEXT
);

CREATE TABLE person (
  id INTEGER CONSTRAINT pk_person PRIMARY KEY AUTOINCREMENT,
  title TEXT,
  firstName TEXT,
  lastName TEXT,
  email TEXT,
  phone TEXT,
  birthDay DATE,
  creditCard INTEGER CONSTRAINT fk_person_to_cc REFERENCES credit_card (id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE employee (
  id INTEGER CONSTRAINT pk_person PRIMARY KEY AUTOINCREMENT,
  title TEXT,
  firstName TEXT,
  lastName TEXT,
  email TEXT,
  phone TEXT,
  birthDay DATE,
  creditCard INTEGER CONSTRAINT fk_person_to_cc REFERENCES creditcard (id) ON DELETE SET NULL ON UPDATE CASCADE,
  company INTEGER CONSTRAINT fk_person_to_company REFERENCES company (id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE person2keymarker (
  person INTEGER CONSTRAINT fk_person2keymarker1 REFERENCES person (id) ON DELETE CASCADE ON UPDATE CASCADE,
  keymarker INTEGER CONSTRAINT fk_person2keymarker2 REFERENCES keymarker (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT pk_person_2_keymarker PRIMARY KEY (person, keymarker)
);
SQL
        );
    }
}
