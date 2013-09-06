Persisted models
================

[![Build Status](https://travis-ci.org/Magomogo/persisted-models.png)](https://travis-ci.org/Magomogo/persisted-models)

Intro
-----

- I don't like ActiveRecord because of tigth coupling with the database,
- I not so comfortable with Data mappers becasue a mapper breaks model encapsulation,
- I like the idea to keep **'The State'** separately from the logic.

Here is the way
---------------

### Clean models

They have only business logic that realized using model's properties. Constructor signature doesn't allow to create an
instance that have no sence from the business logic point of view.

    $person = new Person\Model($propertiesBag);
    $employee = new Employee\Model($company, $propertiesBag);
    
source: [Person\Model](//github.com/Magomogo/persisted-models/blob/master/test/_classes/Test/Person/Model.php "Person model") | [Employee\Model](//github.com/Magomogo/persisted-models/blob/master/test/_classes/Test/Employee/Model.php "Employee model")

### Obvious responsibilities

To achieve persistency we not need to store **A model**, it is necessary to store its properties.

    // save/update
    $dbContainer = new Persisted\Container\SqlDb($connection);
    $person->save($dbContainer);

    // load
    $persistedPerson = Person\Model::load($dbContainer, $id);

source: [Persisted\Container\SqlDb](//github.com/Magomogo/persisted-models/blob/master/lib/Magomogo/Persisted/Container/SqlDb.php "Database container")


Handling user input with **'Forms'**, **'A Form'** is kind of a Container.

    $form = new ProfileForm;
    $person->save($form);

    // final validation here
    $editedPerson = Person\Model::load($form);
    $editedPerson->save($dbContainer);

### Strong separation between different types of object relations.

For example person properties can have a contact info aggregated, it gets stored and updated together with person:

    $contactInfoModel = new ContactInfo\Model($contactInfoProperties);

    $personProperties = array(
        'name' => 'John',
        'contactInfo => $contactInfoModel
    );

In another hand there is a person who working in a company, these objects are connected by foreign key and
created/updated separately.

    $company->save($dbContainer);
    $employee = new Employee\Model($company, $employeeProperties);

    // this won't update company, only creates one to many reference company -> person in the container
    $employee->save($dbContainer);



*...the work is in progress...*
