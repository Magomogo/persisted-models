<?php
namespace Magomogo\Persisted\Exception;

class CollectionName extends \Exception
{
    public function __construct()
    {
        parent::__construct('Collection name isn`t set');
    }
}
