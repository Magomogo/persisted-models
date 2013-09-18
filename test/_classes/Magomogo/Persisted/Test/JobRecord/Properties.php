<?php
namespace Magomogo\Persisted\Test\JobRecord;

use Magomogo\Persisted\PossessionInterface;
use Magomogo\Persisted\PropertyBag;
use Magomogo\Persisted\Test\Company;

/**
 * @property string $id
 */
class Properties extends PropertyBag implements PossessionInterface
{
    /**
     * @var Company\Properties
     */
    private $currentCompanyProps;

    /**
     * @var Company\Properties
     */
    private $previousCompanyProps;

    protected function properties()
    {
        return array();
    }

    protected function init()
    {
        $this->currentCompanyProps = new Company\Properties;
        $this->previousCompanyProps = new Company\Properties;
    }

    public function foreign()
    {
        $foreign = new \stdClass();
        $foreign->currentCompany = $this->currentCompanyProps;
        $foreign->previousCompany = $this->previousCompanyProps;
        return $foreign;
    }

    /**
     * @param PropertyBag $properties
     * @param null|string $relationName
     * @return mixed
     */
    public function ownedBy($properties, $relationName = null)
    {
        $this->{$relationName . 'Props'} = $properties;
        return $this;
    }
}
