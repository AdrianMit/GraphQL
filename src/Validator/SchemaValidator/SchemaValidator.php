<?php

namespace Dreamlabs\GraphQL\Validator\SchemaValidator;

use Dreamlabs\GraphQL\Exception\ConfigurationException;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Schema\AbstractSchema;
use Dreamlabs\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Validator\ConfigValidator\ConfigValidator;

class SchemaValidator
{

    private ?ConfigValidator $configValidator = null;
    /**
     * @throws ConfigurationException
     */
    public function validate(AbstractSchema $schema)
    {
        if (!$schema->getQueryType()->hasFields()) {
            throw new ConfigurationException('Schema has to have fields');
        }

        $this->configValidator = ConfigValidator::getInstance();

        foreach ($schema->getQueryType()->getConfig()->getFields() as $field) {
            $this->configValidator->assertValidConfig($field->getConfig());

            if ($field->getType() instanceof AbstractObjectType) {
                $this->assertInterfaceImplementationCorrect($field->getType());
            }
        }
    }

    /**
     * @throws ConfigurationException
     */
    protected function assertInterfaceImplementationCorrect(AbstractObjectType $type)
    {
        if (!$type->getInterfaces()) {
            return;
        }

        foreach ($type->getInterfaces() as $interface) {
            foreach ($interface->getConfig()->getFields() as $intField) {
                $this->assertFieldsIdentical($intField, $type->getConfig()->getField($intField->getName()), $interface);
            }
        }
    }

    /**
     * @param Field                 $intField
     * @param Field                 $objField
     *
     * @throws ConfigurationException
     */
    protected function assertFieldsIdentical($intField, $objField, AbstractInterfaceType $interface)
    {
        $isValid = true;
        if ($intField->getType()->isCompositeType() !== $objField->getType()->isCompositeType()) {
            $isValid = false;
        }
        if ($intField->getType()->getNamedType()->getName() != $objField->getType()->getNamedType()->getName()) {
            $isValid = false;
        }

        if (!$isValid) {
            throw new ConfigurationException(sprintf('Implementation of %s is invalid for the field %s', $interface->getName(), $objField->getName()));
        }
    }
}
