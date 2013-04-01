<?php
namespace Test\JobRecord;

use Magomogo\Persisted\PersistedAbstract;
use Magomogo\Persisted\Container\ContainerInterface;

class Model extends PersistedAbstract
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
     * @param \Magomogo\Persisted\Container\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom($container, $id)
    {
        $properties = $container->loadProperties(new Properties($id));

        return new self(
            $properties->foreign()->currentCompany,
            $properties->foreign()->previousCompany,
            $properties
        );
    }

    /**
     * @param \Test\Company\Model $currentCompany
     * @param $previousCompany
     * @param Properties $properties
     */
    public function __construct($currentCompany, $previousCompany, $properties)
    {
        $this->currentCompany = $currentCompany;
        $this->previousCompany = $previousCompany;
        $this->properties = $properties;
    }
}
