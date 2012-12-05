<?php
namespace Model;

interface DataSourceInterface
{
    public function readValue($propertyName);
}
