<?php
namespace CreditCard;

class Model
{
    /**
     * @var Properties
     */
    private $properties;

    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    public function maskedPan()
    {
        return substr($this->properties->pan, 0, 4) . ' **** **** ' . substr($this->properties->pan, 12, 4);
    }

    public function paymentSystem()
    {
        return $this->properties->system;
    }

    public function payFor($something)
    {

    }

}
