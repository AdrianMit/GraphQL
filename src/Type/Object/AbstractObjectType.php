<?php

namespace Dreamlabs\GraphQL\Type\Object;


use Dreamlabs\GraphQL\Config\AbstractConfig;
use InvalidArgumentException;
use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Dreamlabs\GraphQL\Type\Traits\AutoNameTrait;
use Dreamlabs\GraphQL\Type\Traits\FieldsArgumentsAwareObjectTrait;
use Dreamlabs\GraphQL\Type\TypeMap;

/**
 * Class AbstractObjectType
 * @package Dreamlabs\GraphQL\Type\Object
 */
abstract class AbstractObjectType extends AbstractType
{
    use AutoNameTrait, FieldsArgumentsAwareObjectTrait;

    protected $isBuilt = false;

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
     * @param $config
     */
    public function __construct(array $config = [])
    {
        if (empty($config)) {
            $config['name']       = $this->getName();
            $config['interfaces'] = $this->getInterfaces();
        }

        $this->config = new ObjectTypeConfig($config, $this);
    }

    final public function serialize($value): mixed
    {
        throw new InvalidArgumentException('You can not serialize object value directly');
    }

    public function getKind(): string
    {
        return TypeMap::KIND_OBJECT;
    }

    public function getType()
    {
        return $this->getConfigValue('type', $this);
    }

    public function getNamedType()
    {
        return $this;
    }
    
    abstract public function build(ObjectTypeConfig $config);

    /**
     * @return AbstractInterfaceType[]
     */
    public function getInterfaces()
    {
        return $this->getConfigValue('interfaces', []);
    }

    public function isValidValue(mixed $value): bool
    {
        return is_array($value) || is_null($value) || is_object($value);
    }

}
