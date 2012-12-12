<?php
namespace JobRecord;
use Magomogo\Model\ContainerReadyInterface;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Company;

class Model implements ContainerReadyInterface
{
    use \Magomogo\Model\ContainerUtils;

    /**
     * @var \Company\Model
     */
    private $previousCompany;

    /**
     * @var \Company\Model
     */
    private $currentCompany;

    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom(ContainerInterface $container, $id)
    {
        $properties = new Properties($id);
        $references = array(
            'currentCompany' => new Company\Properties(),
            'previousCompany' => new Company\Properties()
        );
        $container->loadProperties($properties, $references);

        return new self(
            new Company\Model($references['currentCompany']),
            new Company\Model($references['previousCompany']),
            $properties
        );
    }

    public function __construct(Company\Model $currentCompany, \Company\Model $previousCompany, Properties $properties)
    {
        $this->currentCompany = $currentCompany;
        $this->previousCompany = $previousCompany;
        $this->properties = $properties;
    }

    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @return string unique identifier
     */
    public function putIn(ContainerInterface $container)
    {
        return $container->saveProperties(
            $this->properties,
            array(
                'currentCompany' => $this->currentCompany->propertiesFrom($container),
                'previousCompany' => $this->previousCompany->propertiesFrom($container)
            )
        )->id;

    }
}
