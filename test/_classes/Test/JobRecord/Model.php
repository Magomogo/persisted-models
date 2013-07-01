<?php
namespace Test\JobRecord;

use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\Container\ContainerInterface;
use Test\Company;

class Model implements ModelInterface
{
    /**
     * @var Company\Model
     */
    private $previousCompany;

    /**
     * @var Company\Model
     */
    private $currentCompany;

    /**
     * @param ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function load($container, $id)
    {
        $p = new Properties();
        $p->persisted($id, $container);
        $p->loadFrom($container);
        return new self(
            new Company\Model($p->foreign()->currentCompany),
            new Company\Model($p->foreign()->previousCompany)
        );
    }

    public function save($container)
    {
        $properties = new Properties();
        return $properties->putIn(
            $container,
            $this->currentCompany->propertiesToBeConnectedWith($properties),
            $this->previousCompany->propertiesToBeConnectedWith($properties)
        );
    }

//----------------------------------------------------------------------------------------------------------------------

    /**
     * @param Company\Model $currentCompany
     * @param Company\Model $previousCompany
     * @return Model
     */
    public function __construct($currentCompany, $previousCompany)
    {
        $this->currentCompany = $currentCompany;
        $this->previousCompany = $previousCompany;
    }

    public function description()
    {
        return $this->previousCompany->name() . ' -> ' . $this->currentCompany->name();
    }
}
