<?php
namespace Test\JobRecord;

use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\Container\ContainerInterface;

class Model implements ModelInterface
{
    /**
     * @var Properties
     */
    private $properties;

    /**
     * @var \Test\Company\Model
     */
    private $previousCompany;

    /**
     * @var \Test\Company\Model
     */
    private $currentCompany;

    /**
     * @param ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function load($container, $id = null)
    {
        $p = new Properties();
        $p->persisted($id, $container);
        return $p->loadFrom($container)->constructModel();
    }

    public function putIn($container)
    {
        return $this->properties->putIn($container);
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

    public function description()
    {
        return $this->previousCompany->name() . ' -> ' . $this->currentCompany->name();
    }
}
