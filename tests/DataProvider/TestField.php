<?php

namespace Dreamlabs\Tests\DataProvider;

use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Field\AbstractField;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;

class TestField extends AbstractField
{

    /**
     * @return AbstractObjectType
     */
    public function getType(): IntType
    {
        return new IntType();
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        return $value;
    }

    public function getDescription(): string
    {
        return 'description';
    }
}
