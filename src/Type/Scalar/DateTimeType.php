<?php

namespace Dreamlabs\GraphQL\Type\Scalar;

use DateTimeInterface;
use DateTime;
class DateTimeType extends AbstractScalarType
{

    public function __construct(private $format = 'Y-m-d H:i:s')
    {
    }

    public function getName(): string
    {
        return 'DateTime';
    }

    public function isValidValue(mixed $value): bool
    {
        if ((is_object($value) && $value instanceof DateTimeInterface) || is_null($value)) {
            return true;
        } else if (is_string($value)) {
            $date = $this->createFromFormat($value);
        } else {
            $date = null;
        }

        return $date ? true : false;
    }

    public function serialize($value): mixed
    {
        $date = null;

        if (is_string($value)) {
            $date = $this->createFromFormat($value);
        } elseif ($value instanceof DateTimeInterface) {
            $date = $value;
        }

        return $date ? $date->format($this->format) : null;
    }

    public function parseValue($value): mixed
    {
        if (is_string($value)) {
            $date = $this->createFromFormat($value);
        } elseif ($value instanceof DateTimeInterface) {
            $date = $value;
        } else {
            $date = false;
        }

        return $date ?: null;
    }

    private function createFromFormat(string $value)
    {
        return DateTime::createFromFormat($this->format, $value);
    }

    public function getDescription(): string
    {
        return sprintf('Representation of date and time in "%s" format', $this->format);
    }

}
