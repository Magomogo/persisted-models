<?php
namespace CreditCard;
use Model\PropertyBag;

class Model
{
    /**
     * @var PropertyBag
     */
    private $properties;

    public function __construct(PropertyBag $properties)
    {
        $this->properties = $properties;
    }

    public function payFor($something)
    {

    }

}
