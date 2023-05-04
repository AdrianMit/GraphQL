<?php

namespace Dreamlabs\GraphQL\Config\Traits;


use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Exception\ValidationException;
use Dreamlabs\GraphQL\Field\AbstractField;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Field\FieldInterface;
use Dreamlabs\GraphQL\Field\InputFieldInterface;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\InterfaceType\AbstractInterfaceType;

/**
 * Class FieldsAwareTrait
 * @package Dreamlabs\GraphQL\Config\Traits
 */
trait FieldsAwareConfigTrait
{
    protected array $fields = [];
    
    /**
     * @throws ConfigurationException
     */
    public function buildFields(): void
    {
        if (!empty($this->data['fields'])) {
            $this->addFields($this->data['fields']);
        }
    }
    
    /**
     * @throws ConfigurationException
     */
    public function applyInterface(AbstractInterfaceType $interfaceType): static
    {
        $this->addFields($interfaceType->getFields());

        return $this;
    }
    
    /**
     * @throws ConfigurationException
     */
    public function addFields(array $fieldsList): static
    {
        foreach ($fieldsList as $fieldName => $fieldConfig) {

            if ($fieldConfig instanceof FieldInterface) {
                $this->fields[$fieldConfig->getName()] = $fieldConfig;
                continue;
            } elseif($fieldConfig instanceof InputFieldInterface) {
                $this->fields[$fieldConfig->getName()] = $fieldConfig;
                continue;
            } else {
                $this->addField($fieldName, $this->buildFieldConfig($fieldName, $fieldConfig));
            }
        }

        return $this;
    }
    
    /**
     * @throws ConfigurationException
     */
    public function addField(AbstractField|string $field, AbstractType|array|string|null $fieldInfo = null): static
    {
        if (!($field instanceof FieldInterface)) {
            $field = new Field($this->buildFieldConfig($field, $fieldInfo));
        }

        if ($this->hasField($field->getName())) {
            throw new ConfigurationException(sprintf('Type "%s" was defined more than once', $field->getName()));
        }
        
        $this->fields[$field->getName()] = $field;

        return $this;
    }

    protected function buildFieldConfig(string $name, mixed $info = null)
    {
        if (!is_array($info)) {
            $info = [
                'type' => $info,
                'name' => $name,
            ];
        } elseif (empty($info['name'])) {
            $info['name'] = $name;
        }

        return $info;
    }
    
    public function getField(string $name): ?FieldInterface
    {
        return $this->hasField($name) ? $this->fields[$name] : null;
    }

    public function hasField(string $name): bool
    {
        return array_key_exists($name, $this->fields);
    }

    public function hasFields(): bool
    {
        return !empty($this->fields);
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function removeField(string $name): static
    {
        if ($this->hasField($name)) {
            unset($this->fields[$name]);
        }

        return $this;
    }
}
