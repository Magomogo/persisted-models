<?php
namespace Magomogo\Persisted\Test\Affiliate;

use Magomogo\Persisted\AbstractProperties;

class Properties extends AbstractProperties
{
    public $id;
    public $name;
    public $cookie;

    protected function init()
    {
        $this->cookie = new Cookie\Model(new Cookie\Properties());
    }

    public function naturalKeyFieldName()
    {
        return 'id';
    }
}
