<?php
namespace Test\JobRecord;

use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\Container\ContainerInterface;

class Model implements ModelInterface
{
    /**
     * @var \Test\Company\Model
     */
    private $previousCompany;

    /**
     * @var \Test\Company\Model
     */
    private $currentCompany;

    /**
     * @param string $id
     * @return Properties
     */
    public static function newPropertyBag($id = null)
    {
        return new Properties($id);
    }

    /**
     * @param ContainerInterface $container
     * @return Properties
     */
    public function propertiesFor($container)
    {
        return $this->properties;
    }

//----------------------------------------------------------------------------------------------------------------------

    /**
     * @param \Test\Company\Model $currentCompany
     * @param \Test\Company\Model $previousCompany
     * @param Properties $properties
     * @return \Test\JobRecord\Model
     */
    public function __construct($currentCompany, $previousCompany, $properties)
    {
        $this->currentCompany = $currentCompany;
        $this->previousCompany = $previousCompany;
        $this->properties = $properties;
    }
}
