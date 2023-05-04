<?php

namespace Dreamlabs\Tests\DataProvider;


use Dreamlabs\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class TestInterfaceType extends AbstractInterfaceType
{

    public function resolveType($object)
    {
        return is_object($object) ? $object : new TestObjectType();
    }

    public function build($config): void
    {
        $config->addField('name', new StringType());
    }


}
