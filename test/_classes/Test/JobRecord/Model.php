<?php
namespace Test\JobRecord;

use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\Container\ContainerInterface;
use Test\Company;

class Model implements ModelInterface
{
    /**
     * @var Properties
     */
    private $properties;

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
        return $this->properties->putIn($container);
    }

//----------------------------------------------------------------------------------------------------------------------

    /**
     * @param Company\Model $currentCompany
     * @param Company\Model $previousCompany
     * @return Model
     */
    public function __construct($currentCompany, $previousCompany)
    {
        $this->properties = new Properties();
        $this->currentCompany = $currentCompany->connectToAJobRecord($this->properties, 'currentCompany');
        $this->previousCompany = $previousCompany->connectToAJobRecord($this->properties, 'previousCompany');
    }

    public function description()
    {
        return $this->previousCompany->name() . ' -> ' . $this->currentCompany->name();
    }
}
