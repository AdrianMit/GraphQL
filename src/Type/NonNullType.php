<?php

namespace Dreamlabs\GraphQL\Type;


use Dreamlabs\GraphQL\Config\Traits\ConfigAwareTrait;
use Dreamlabs\GraphQL\Exception\ConfigurationException;

final class NonNullType extends AbstractType implements CompositeTypeInterface
{
    use ConfigAwareTrait;

    private $_typeOf;

    /**
     * NonNullType constructor.
     *
     * @param AbstractType|string $fieldType
     *
     * @throws ConfigurationException
     */
    public function __construct($fieldType)
    {
        if (!TypeService::isGraphQLType($fieldType)) {
            throw new ConfigurationException('NonNullType accepts only GraphpQL Types as argument');
        }
        if (TypeService::isScalarType($fieldType)) {
            $fieldType = TypeFactory::getScalarType($fieldType);
        }

        $this->_typeOf = $fieldType;
    }

    public function getName(): ?string
    {
        return null;
    }

    public function getKind(): string
    {
        return TypeMap::KIND_NON_NULL;
    }

    public function resolve($value)
    {
        return $value;
    }

    public function isValidValue(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }

        return $this->getNullableType()->isValidValue($value);
    }

    public function isCompositeType()
    {
        return true;
    }

    public function isInputType()
    {
        return true;
    }

    public function getNamedType()
    {
        return $this->getTypeOf();
    }

    public function getNullableType()
    {
        return $this->getTypeOf();
    }

    public function getTypeOf()
    {
        return $this->_typeOf;
    }

    public function parseValue($value): mixed
    {
        return $this->getNullableType()->parseValue($value);
    }

    public function getValidationError($value = null)
    {
        if ($value === null) {
            return 'Field must not be NULL';
        }
        return $this->getNullableType()->getValidationError($value);
    }


}
