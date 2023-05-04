<?php

namespace Dreamlabs\GraphQL\Type\Scalar;

class FloatType extends AbstractScalarType
{

    public function getName(): string
    {
        return 'Float';
    }

    public function serialize($value): mixed
    {
        if ($value === null) {
            return null;
        } else {
            return floatval($value);
        }
    }

    public function isValidValue(mixed $value): bool
    {
        return is_null($value) || is_float($value) || is_int($value);
    }

    public function getDescription(): string
    {
        return 'The `Float` scalar type represents signed double-precision fractional ' .
               'values as specified by ' .
               '[IEEE 754](http://en.wikipedia.org/wiki/IEEE_floating_point).';
    }

}
