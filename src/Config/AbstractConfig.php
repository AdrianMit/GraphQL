<?php

namespace Dreamlabs\GraphQL\Config;


use Exception;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Exception\ValidationException;
use Dreamlabs\GraphQL\Validator\ConfigValidator\ConfigValidator;

/**
 * Class Config
 *
 * @package Dreamlabs\GraphQL\Config
 */
abstract class AbstractConfig
{
    protected array $data;
    
    protected ?bool $extraFieldsAllowed = null;
    
    /**
     * @throws ConfigurationException
     */
    public function __construct(array $configData, protected ?object $contextObject = null, protected $finalClass = false)
    {
        if (empty($configData)) {
            throw new ConfigurationException('Config for Type should be an array');
        }
        $this->data = $configData;
        
        $this->build();
    }
    
    /**
     * @throws ConfigurationException
     */
    public function validate(): void
    {
        $validator = ConfigValidator::getInstance();
        
        if ( ! $validator->validate($this->data, $this->getContextRules(), $this->extraFieldsAllowed)) {
            throw new ConfigurationException(
                'Config is not valid for ' . ($this->contextObject ? $this->contextObject::class : null) . "\n" . implode(
                    "\n",
                    $validator->getErrorsArray(false)
                )
            );
        }
    }
    
    public function getContextRules(): array
    {
        $rules = $this->getRules();
        if ($this->finalClass) {
            foreach ($rules as $name => $info) {
                if ( ! empty($info['final'])) {
                    $rules[$name]['required'] = true;
                }
            }
        }
        
        return $rules;
    }
    
    abstract public function getRules();
    
    public function getName(): mixed
    {
        return $this->get('name');
    }
    
    public function getType(): mixed
    {
        return $this->get('type');
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function getContextObject(): ?object
    {
        return $this->contextObject;
    }
    
    public function isFinalClass(): bool
    {
        return $this->finalClass;
    }
    
    public function isExtraFieldsAllowed(): ?bool
    {
        return $this->extraFieldsAllowed;
    }

    public function getResolveFunction(): ?callable
    {
        return $this->get('resolve', null);
    }
    
    protected function build()
    {
    }

    public function get(string $key, mixed $defaultValue = null): mixed
    {
        return $this->has($key) ? $this->data[$key] : $defaultValue;
    }
    
    public function set(string $key, mixed $value): static
    {
        $this->data[$key] = $value;
        
        return $this;
    }
    
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }
    
    /**
     * @throws Exception
     */
    public function __call(string $method, array $arguments)
    {
        if (str_starts_with($method, 'get')) {
            $propertyName = lcfirst(substr($method, 3));
        } elseif (str_starts_with($method, 'set')) {
            $propertyName = lcfirst(substr($method, 3));
            $this->set($propertyName, $arguments[0]);
            
            return $this;
        } elseif (str_starts_with($method, 'is')) {
            $propertyName = lcfirst(substr($method, 2));
        } else {
            throw new Exception('Call to undefined method ' . $method);
        }
        
        return $this->get($propertyName);
    }
}
