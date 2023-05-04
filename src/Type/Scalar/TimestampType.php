<?php

namespace Dreamlabs\GraphQL\Type\Scalar;


/**
 * Class TimestampType
 * @package Dreamlabs\GraphQL\Type\Scalar
 * @deprecated Should not be used, to be removed in 1.5
 */
class TimestampType extends AbstractScalarType
{

    public function getName(): string
    {
        return 'Timestamp';
    }

    /**
     * @param $value \DateTime
     */
    public function serialize($value): ?string
    {
        if ($value === null || !is_object($value)) {
            return null;
        }

        return $value->getTimestamp();
    }

    public function isValidValue(mixed $value): bool
    {
        if (is_null($value) || is_object($value)) {
            return true;
        }

        return is_int($value);
    }

    public function getDescription(): string
    {
        return 'DEPRECATED. Will be converted to a real timestamp';
    }

}
