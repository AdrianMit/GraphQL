<?php

namespace Dreamlabs\GraphQL\Config\Object;


use Dreamlabs\GraphQL\Config\AbstractConfig;
use Dreamlabs\GraphQL\Config\Traits\ArgumentsAwareConfigTrait;
use Dreamlabs\GraphQL\Config\Traits\FieldsAwareConfigTrait;
use Dreamlabs\GraphQL\Config\TypeConfigInterface;
use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Type\TypeService;

/**
 * Class InterfaceTypeConfig
 * @package Dreamlabs\GraphQL\Config\Object
 * @method $this setDescription(string $description)
 */
class InterfaceTypeConfig extends AbstractConfig implements TypeConfigInterface
{
    use FieldsAwareConfigTrait, ArgumentsAwareConfigTrait;

    public function getRules(): array
    {
        return [
            'name'        => ['type' => TypeService::TYPE_STRING, 'final' => true],
            'fields'      => ['type' => TypeService::TYPE_ARRAY_OF_FIELDS_CONFIG, 'final' => true],
            'description' => ['type' => TypeService::TYPE_STRING],
            'resolveType' => ['type' => TypeService::TYPE_CALLABLE, 'final' => true],
        ];
    }

    protected function build(): void
    {
        $this->buildFields();
    }
    
    /**
     * @throws ConfigurationException
     */
    public function resolveType(mixed $object)
    {
        $callable = $this->get('resolveType');

        if ($callable && is_callable($callable)) {
            return $callable($object);
        } elseif (is_callable([$this->contextObject, 'resolveType'])) {
            return $this->contextObject->resolveType($object);
        }

        throw new ConfigurationException('There is no valid resolveType for ' . $this->getName());
    }
}
