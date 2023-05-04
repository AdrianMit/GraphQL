<?php

namespace Dreamlabs\GraphQL\Type\Scalar;

use DateTime;
/**
 * @deprecated USE DateTime type instead. To be removed in 1.4.
 *
 * Class DateType
 * @package Dreamlabs\GraphQL\Type\Scalar
 */
class DateType extends AbstractScalarType
{

    public function getName(): string
    {
        return 'Date';
    }

    /**
     * @param $value \DateTime
     */
    public function serialize($value): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value->format('Y-m-d');
    }

    public function isValidValue(mixed $value): bool
    {
        if (is_null($value) || is_object($value)) {
            return true;
        }

        $d = DateTime::createFromFormat('Y-m-d', $value);

        return $d && $d->format('Y-m-d') == $value;
    }

    public function getDescription(): string
    {
        return 'DEPRECATED. Use DateTime instead';
    }

}
