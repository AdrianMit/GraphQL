<?php

namespace Dreamlabs\GraphQL\Type\Scalar;


class IdType extends AbstractScalarType
{

    public function getName(): string
    {
        return 'ID';
    }

    public function serialize($value): mixed
    {
        if (null === $value) {
            return null;
        }

        return (string)$value;
    }

    public function getDescription(): string
    {
        return 'The `ID` scalar type represents a unique identifier, often used to ' .
               'refetch an object or as key for a cache. The ID type appears in a JSON ' .
               'response as a String; however, it is not intended to be human-readable. ' .
               'When expected as an input type, any string (such as `"4"`) or integer ' .
               '(such as `4`) input value will be accepted as an ID.';
    }
}
