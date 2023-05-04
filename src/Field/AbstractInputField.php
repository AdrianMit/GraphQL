<?php

namespace Dreamlabs\GraphQL\Field;


use Dreamlabs\GraphQL\Config\Field\InputFieldConfig;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\InputTypeInterface;
use Dreamlabs\GraphQL\Type\Traits\AutoNameTrait;
use Dreamlabs\GraphQL\Type\Traits\FieldsArgumentsAwareObjectTrait;
use Dreamlabs\GraphQL\Type\TypeFactory;
use Dreamlabs\GraphQL\Type\TypeService;

abstract class AbstractInputField implements InputFieldInterface
{
    use FieldsArgumentsAwareObjectTrait, AutoNameTrait;
    
    protected bool $isFinal = false;
    
    /**
     * @throws ConfigurationException
     */
    public function __construct(array $config = [])
    {
        if (empty($config['type'])) {
            $config['type'] = $this->getType();
            $config['name'] = $this->getName();
        }
        
        if (TypeService::isScalarType($config['type'])) {
            $config['type'] = TypeFactory::getScalarType($config['type']);
        }
        
        $this->config = new InputFieldConfig($config, $this, $this->isFinal);
        $this->build($this->config);
    }
    
    public function build(InputFieldConfig $config): void
    {
    }

    abstract public function getType(): AbstractType;
    
    public function getDefaultValue()
    {
        return $this->config->getDefaultValue();
    }
    
    //todo: think about serialize, parseValue methods
    
}
