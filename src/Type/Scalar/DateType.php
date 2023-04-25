<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\Scalar;

use DateTime;
/**
 * @deprecated USE DateTime type instead. To be removed in 1.4.
 *
 * Class DateType
 * @package Youshido\GraphQL\Type\Scalar
 */
class DateType extends AbstractScalarType
{

    public function getName()
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

    public function isValidValue($value)
    {
        if (is_null($value) || is_object($value)) {
            return true;
        }

        $d = DateTime::createFromFormat('Y-m-d', $value);

        return $d && $d->format('Y-m-d') == $value;
    }

    public function getDescription()
    {
        return 'DEPRECATED. Use DateTime instead';
    }

}
