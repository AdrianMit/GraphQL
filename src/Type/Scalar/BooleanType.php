<?php

namespace Dreamlabs\GraphQL\Type\Scalar;


class BooleanType extends AbstractScalarType
{
    public function getName(): string
    {
        return 'Boolean';
    }

    public function serialize($value): mixed
    {
        if ($value === null) {
            return null;
        }
        if ($value === 'true') {
            return true;
        }
        if ($value === 'false') {
            return false;
        }

        return (bool)$value;
    }

    public function isValidValue(mixed $value): bool
    {
        return is_null($value) || is_bool($value);
    }

    public function getDescription(): string
    {
        return 'The `Boolean` scalar type represents `true` or `false`.';
    }

}
