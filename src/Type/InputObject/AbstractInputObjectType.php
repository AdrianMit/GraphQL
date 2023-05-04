<?php

namespace Dreamlabs\GraphQL\Type\InputObject;


use Dreamlabs\GraphQL\Config\AbstractConfig;
use Exception;
use Dreamlabs\GraphQL\Config\Object\InputObjectTypeConfig;
use Dreamlabs\GraphQL\Field\InputFieldInterface;
use Dreamlabs\GraphQL\Parser\Ast\ArgumentValue\InputObject;
use Dreamlabs\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\Traits\AutoNameTrait;
use Dreamlabs\GraphQL\Type\Traits\FieldsAwareObjectTrait;
use Dreamlabs\GraphQL\Type\TypeMap;

abstract class AbstractInputObjectType extends AbstractType
{

    use AutoNameTrait, FieldsAwareObjectTrait;

    protected bool $isBuilt = false;

    public function getConfig(): AbstractConfig
    {
        if (!$this->isBuilt) {
            $this->isBuilt = true;
            $this->build($this->config);
        }

        return $this->config;
    }

    public function __construct($config = [])
    {
        if (empty($config)) {
            $config = [
                'name' => $this->getName()
            ];
        }
        $this->config = new InputObjectTypeConfig($config, $this);
    }

    /**
     * @param InputObjectTypeConfig $config
     */
    abstract public function build($config);

    public function isValidValue(mixed $value): bool
    {
        if ($value instanceof InputObject) {
            $value = $value->getValue();
        }

        if (empty($value)) {
            return true;
        }

        if (!is_array($value)) {
            return false;
        }

        $typeConfig     = $this->getConfig();
        $requiredFields = array_filter($typeConfig->getFields(), fn(InputFieldInterface $field): bool => $field->getType()->getKind() == TypeMap::KIND_NON_NULL);

        foreach ($value as $valueKey => $valueItem) {
            if (!$typeConfig->hasField($valueKey)) {
                // Schema validation will generate the error message for us.
                return false;
            }

            $field = $typeConfig->getField($valueKey);
            if (!$field->getType()->isValidValue($valueItem)) {
                $error                     = $field->getType()->getValidationError($valueItem) ?: '(no details available)';
                $this->lastValidationError = sprintf('Not valid type for field "%s" in input type "%s": %s', $field->getName(), $this->getName(), $error);
                return false;
            }

            if (array_key_exists($valueKey, $requiredFields)) {
                unset($requiredFields[$valueKey]);
            }
        }
        if (count((array) $requiredFields)) {
            $this->lastValidationError = sprintf('%s %s required on %s', implode(', ', array_keys($requiredFields)), count((array) $requiredFields) > 1 ? 'are' : 'is', $typeConfig->getName());
        }

        return !(count((array) $requiredFields) > 0);
    }

    public function getKind(): string
    {
        return TypeMap::KIND_INPUT_OBJECT;
    }

    public function isInputType(): bool
    {
        return true;
    }

    public function parseValue($value): mixed
    {
        if (is_null($value)) return null;
        if($value instanceof InputObject) {
            $value = $value->getValue();
        }

        $typeConfig = $this->getConfig();
        foreach ($value as $valueKey => $item) {
            if ($item instanceof Variable) {
                $item = $item->getValue();
            }

            if (!($inputField = $typeConfig->getField($valueKey))) {
                throw new Exception(sprintf('Invalid field "%s" on %s', $valueKey, $typeConfig->getName()));
            }
            $value[$valueKey] = $inputField->getType()->parseValue($item);
        }

        return $value;
    }

}
