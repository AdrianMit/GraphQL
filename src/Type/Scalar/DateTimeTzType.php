<?php

namespace Dreamlabs\GraphQL\Type\Scalar;

use DateTimeInterface;
use DateTime;
class DateTimeTzType extends AbstractScalarType
{
    private string $format = 'D, d M Y H:i:s O';

    public function getName(): string
    {
        return 'DateTimeTz';
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
        return 'Representation of date and time in "r" format';
    }

}
