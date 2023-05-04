<?php

namespace Dreamlabs\GraphQL\Type\Union;


use Dreamlabs\GraphQL\Config\Object\UnionTypeConfig;
use Dreamlabs\GraphQL\Config\Traits\ConfigAwareTrait;
use Dreamlabs\GraphQL\Type\AbstractInterfaceTypeInterface;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\AbstractScalarType;
use Dreamlabs\GraphQL\Type\Traits\AutoNameTrait;
use Dreamlabs\GraphQL\Type\TypeMap;

abstract class AbstractUnionType extends AbstractType implements AbstractInterfaceTypeInterface
{

    use ConfigAwareTrait, AutoNameTrait;

    protected bool $isFinal = false;

    /**
     * ObjectType constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config['name']  = $this->getName();
            $config['types'] = $this->getTypes();
        }

        $this->config = new UnionTypeConfig($config, $this, $this->isFinal);
    }

    /**
     * @return AbstractObjectType[]|AbstractScalarType[]
     */
    abstract public function getTypes();

    public function getKind(): string
    {
        return TypeMap::KIND_UNION;
    }

    public function getNamedType()
    {
        return $this;
    }

    public function isValidValue(mixed $value): bool
    {
        return true;
    }

}
