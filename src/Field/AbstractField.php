<?php

namespace Dreamlabs\GraphQL\Field;

use Dreamlabs\GraphQL\Config\Field\FieldConfig;
use Dreamlabs\GraphQL\Config\Traits\ResolvableObjectTrait;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Traits\AutoNameTrait;
use Dreamlabs\GraphQL\Type\Traits\FieldsArgumentsAwareObjectTrait;
use Dreamlabs\GraphQL\Type\TypeFactory;
use Dreamlabs\GraphQL\Type\TypeService;

abstract class AbstractField implements FieldInterface
{
    use FieldsArgumentsAwareObjectTrait;
    use ResolvableObjectTrait;
    use AutoNameTrait {
        getName as getAutoName;
    }
    
    protected bool $isFinal = false;
    
    private mixed $nameCache = null;
    
    /**
     * @throws ConfigurationException
     */
    public function __construct(array $config = [])
    {
        if (empty($config['type'])) {
            $config['type'] = $this->getType();
            $config['name'] = $this->getName();
            if (empty($config['name'])) {
                $config['name'] = $this->getAutoName();
            }
        }
        
        if (TypeService::isScalarType($config['type'])) {
            $config['type'] = TypeFactory::getScalarType($config['type']);
        }
        $this->nameCache = $config['name'] ?? $this->getAutoName();
        
        $this->config = new FieldConfig($config, $this, $this->isFinal);
        $this->build($this->config);
    }
    
    abstract public function getType(): AbstractType|AbstractObjectType;
    
    public function build(FieldConfig $config): void
    {
    }
    
    public function setType($type): void
    {
        $this->getConfig()->set('type', $type);
    }
    
    public function getName()
    {
        return $this->nameCache;
    }
    
    public function isDeprecated()
    {
        return $this->getConfigValue('isDeprecated', false);
    }
    
    public function getDeprecationReason()
    {
        return $this->getConfigValue('deprecationReason');
    }
}
