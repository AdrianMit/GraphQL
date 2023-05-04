<?php

namespace Dreamlabs\GraphQL\Type\InterfaceType;


use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Config\Object\InterfaceTypeConfig;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\Traits\AutoNameTrait;
use Dreamlabs\GraphQL\Type\Traits\FieldsAwareObjectTrait;
use Dreamlabs\GraphQL\Type\TypeInterface;
use Dreamlabs\GraphQL\Type\TypeMap;

abstract class AbstractInterfaceType extends AbstractType
{
    use FieldsAwareObjectTrait, AutoNameTrait;

    protected bool $isBuilt = false;

    public function getConfig(): AbstractConfig
    {
        if (!$this->isBuilt) {
            $this->isBuilt = true;
            $this->build($this->config);
        }

        return $this->config;
    }

    /**
     * ObjectType constructor.
     *
     * @param $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config['name'] = $this->getName();
        }

        $this->config = new InterfaceTypeConfig($config, $this);
    }

    abstract public function resolveType($object);

    /**
     * @param InterfaceTypeConfig $config
     */
    abstract public function build($config);

    public function getKind(): string
    {
        return TypeMap::KIND_INTERFACE;
    }

    public function getNamedType()
    {
        return $this;
    }

    public function isValidValue(mixed $value): bool
    {
        return is_array($value) || is_null($value) || is_object($value);
    }

    /**
     * @return TypeInterface[] an array of types that implement this interface. Used mainly for introspection and 
     *                         documentation generation.
     */
    public function getImplementations()
    {
        return [];
    }
}
