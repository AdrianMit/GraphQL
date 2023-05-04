<?php

namespace Dreamlabs\GraphQL\Type\Scalar;

use Dreamlabs\GraphQL\Config\Traits\ConfigAwareTrait;
use Dreamlabs\GraphQL\Type\AbstractType;
use Dreamlabs\GraphQL\Type\TypeMap;

abstract class AbstractScalarType extends AbstractType
{
    use ConfigAwareTrait;

    public function getName(): string
    {
        $className = static::class;

        return substr($className, strrpos($className, '\\') + 1, -4);
    }

    final public function getKind(): string
    {
        return TypeMap::KIND_SCALAR;
    }

    public function parseValue($value): mixed
    {
        return $this->serialize($value);
    }

    public function isInputType(): bool
    {
        return true;
    }


}
