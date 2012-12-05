<?php
namespace Model\DataType;

interface DataTypeInterface
{
    public function setValue($value);
    public function value();
}
