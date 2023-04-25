<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 11:05 PM
*/

namespace Youshido\GraphQL\Config\Traits;


use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Exception\ValidationException;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Field\InputFieldInterface;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;

/**
 * Class FieldsAwareTrait
 * @package Youshido\GraphQL\Config\Traits
 */
trait FieldsAwareConfigTrait
{
    protected array $fields = [];

    public function buildFields(): void
    {
        if (!empty($this->data['fields'])) {
            $this->addFields($this->data['fields']);
        }
    }

    /**
     * Add fields from passed interface
     * @return $this
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
    public function addField(Field|string $field, array $fieldInfo = null): static
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
    
    public function getField(string $name): ?Field
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
