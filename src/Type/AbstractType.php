<?php

namespace Dreamlabs\GraphQL\Type;


use Stringable;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;

abstract class AbstractType implements TypeInterface, Stringable
{
    protected $lastValidationError = null;
    public function isCompositeType()
    {
        return false;
    }
    /**
     * @return AbstractType
     */
    public function getType()
    {
        return $this;
    }
    /**
     * @return AbstractType
     */
    public function getNamedType()
    {
        return $this->getType();
    }
    /**
     * @return AbstractType|AbstractObjectType
     */
    public function getNullableType()
    {
        return $this;
    }
    public function getValidationError($value = null)
    {
        return $this->lastValidationError;
    }
    public function isValidValue(mixed $value): bool
    {
        return true;
    }
    public function parseValue(mixed $value): mixed
    {
        return $value;
    }
    public function parseInputValue($value)
    {
        return $this->parseValue($value);
    }
    public function serialize($value): mixed
    {
        return $value;
    }
    public function isInputType()
    {
        return false;
    }
    public function __toString(): string
    {
        return $this->getName();
    }
}
