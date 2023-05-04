<?php

namespace Dreamlabs\Tests\DataProvider;


use DateTime;
use Dreamlabs\GraphQL\Type\Scalar\AbstractScalarType;

class TestTimeType extends AbstractScalarType
{

    public function getName(): string
    {
        return 'TestTime';
    }

    /**
     * @param $value \DateTime
     */
    public function serialize($value): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof DateTime ? $value->format('H:i:s') : $value;
    }

    public function isValidValue(mixed $value): bool
    {
        if (is_object($value)) {
            return true;
        }

        $d = DateTime::createFromFormat('H:i:s', $value);

        return $d && $d->format('H:i:s') == $value;
    }

    public function getDescription(): string
    {
        return 'Representation time in "H:i:s" format';
    }

}
