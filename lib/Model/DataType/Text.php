<?php
namespace Model\DataType;

class Text implements DataTypeInterface
{
    private $value;

    public function __construct($defaultValue = null)
    {
        $this->value = $defaultValue;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function value()
    {
        return $this->value;
    }
}
