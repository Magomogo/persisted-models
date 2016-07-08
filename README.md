Persisted models
================

[![Build Status](https://travis-ci.org/Magomogo/persisted-models.png?branch=master)](https://travis-ci.org/Magomogo/persisted-models)

Intro
-----

- I don't like ActiveRecord because of tight coupling with the database,
- I am not so comfortable with Data mappers because a mapper breaks model encapsulation,
- I like the idea to keep **'The State'** separately from the logic.

Here is the way
---------------

### The concept

To implement an entity, one should create two classes: **Model** and **Properties**. Model class contains domain-specific
logic only. All **state** that should persist is located in **Properties**. Model has its Properties aggregated:

    class Model implements ModelInterface
    {
        /**
         * @var Properties
         */
        private $properties;

        public function __construct($properties)
        {
             $this->properties = $properties;
        }
    }

This library takes care about the **Properties**, all its public fields and also relations can be saved/loaded in
the **Container**s. Currently there is SqlDb, CouchDb, and Memory container.

It is recommended to declare Model's constructor signature that doesn't allow to create an
instance that makes no sense from the business logic point of view.

    $person = new Person\Model($propertiesBag);
    $employee = new Employee\Model($company, $propertiesBag);

source: [Person\Model](//github.com/Magomogo/persisted-models/blob/master/test/_classes/Magomogo/Persisted/Test/Person/Model.php "Person model")
 | [Employee\Model](//github.com/Magomogo/persisted-models/blob/master/test/_classes/Magomogo/Persisted/Test/Employee/Model.php "Employee model")

### Obvious responsibilities

To achieve persistency we don't need to store **A model**, it is necessary to store its properties.

    // save/update
    $dbContainer = new Persisted\Container\SqlDb($connection);
    $person->save($dbContainer);

    // load
    $persistedPerson = Person\Model::load($dbContainer, $id);

source: [Persisted\Container\SqlDb](//github.com/Magomogo/persisted-models/blob/master/lib/Magomogo/Persisted/Container/SqlDb.php "Database container")

Handling user input with **'Editors'**, **'A Editor'** is kind of a Container.

    $editor = new ProfileEditor($person);
    // validation here
    $editor->edit($userInput);

    $editedPerson = Person\Model::load($editor);
    $editedPerson->save($dbContainer);

### Strong separation between different types of object relations

For example person properties can have contact info aggregated, it gets stored and updated together with person:

    $contactInfoModel = new ContactInfo\Model($contactInfoProperties);

    $personProperties = array(
        'name' => 'John',
        'contactInfo => $contactInfoModel
    );

On the other hand, there is a person who's working in a company. These objects are connected by foreign key and
created/updated separately.

    $company->save($dbContainer);
    $employee = new Employee\Model($company, $employeeProperties);

    // this won't update the company, but create one-to-many reference company -> person in the container
    $employee->save($dbContainer);

A model can have a list of another models connected. This so-called many-to-many relation is possible using
Collections.

    $collection = new Keymarker\Collection;
    $collection['Example'] = new Keymarker\Model(new Keymarker\Properties(array('name' => 'Example'));

Examples
--------

See test cases to learn recommended usage:

- Simply a Model with properties [Company/ModelTest.php](//github.com/Magomogo/persisted-models/blob/master/test/Company/ModelTest.php)
- A Person having CreditCard aggregated, a Person that can be tagged with Keymarkers
 [Person/ModelTest.php](//github.com/Magomogo/persisted-models/blob/master/test/Person/ModelTest.php)
- An Employee working in a Company [Employee/ModelTest.php](//github.com/Magomogo/persisted-models/blob/master/test/Employee/ModelTest.php)
- Keymarker model that has natural keys
 [Keymarker/ModelTest.php](//github.com/Magomogo/persisted-models/blob/master/test/Keymarker/ModelTest.php),
 [Keymarker/PropertiesTest.php](//github.com/Magomogo/persisted-models/blob/master/test/Keymarker/PropertiesTest.php)
- Load/Create/Update/Save a persistent model [ModelPersonEditorWorkflowTest.php](//github.com/Magomogo/persisted-models/blob/master/test/ModelPersonEditorWorkflowTest.php)

*...the work is in progress...*
