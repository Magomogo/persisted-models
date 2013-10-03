<?php
namespace Magomogo\Persisted\Test\Keymarker;

use Magomogo\Persisted\AbstractProperties;

class Properties extends AbstractProperties
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var \DateTime
     */
    public $created;

    protected function init()
    {
        $this->created = new \DateTime(date('c'));
    }

    public function naturalKeyFieldName()
    {
        return 'name';
    }
}
