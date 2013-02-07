<?php
namespace Employee;
use Person\Properties as PersonProperties;
use Company\Properties as CompanyProperties;

/**
 * @property string $title
 * @property string $firstName
 * @property string $lastName
 * @property string $phone
 * @property string $email
 * @property \CreditCard\Model $creditCard
 */
class Properties extends PersonProperties
{

}
