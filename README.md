The Best Domain Model ever
==========================

Intro
-----

- I don't like ActiveRecord because of tigth coupling with the database,
- I not so comfortable with Data mappers becasue a mapper breaks model encapsulation,
- I'm afraid of **'The State'** and want to keep it separately from the logic.

Here is the way
---------------

### Clean models

They have only business logic that realized using model's properties. Constructor signature doesn't allow to create an
instance that have no sence from the business logic point of view.

    $person = new Person\Model($propertiesBag);
    $employee = new Employee\Model($company, $propertiesBag);

### Obvious responsibilities

To achieve persistency we not need to store **A model**, it is necessary to store its properties.

    // save/update
    $dbContainer = new Model\DataContainer\Db($connection);
    $person->putIn($dbContainer);

    // load
    $persistedPerson = Person\Model::loadFrom($dbContainer, $id);

Handling user input with **'Forms'**, **'A Form'** is kind of a DataContainer.

    $form = new ProfileForm;
    $person->putIn($form);

    // final validation here
    $editedPerson = Person\Model::loadFrom($form);
    $editedPerson->putIn($dbContainer);

### Strong separation between different types of object relations.

For example person properties can have a contact info aggregated, it gets stored and updated together with person:

    $contactInfoModel = new ContactInfo\Model($contactInfoProperties);

    $personProperties = array(
        'name' => 'John',
        'contactInfo => $contactInfoModel
    );

In another hand there is a person who working in a company, these objects are connected by foreign key and
created/updated separately.

    $company->putIn($dbContainer);
    $employee = new Employee\Model($company, $employeeProperties);

    // this won't update company, only creates one to many reference company -> person in the container
    $employee->putIn($dbContainer);



*...the work is in progress...*
