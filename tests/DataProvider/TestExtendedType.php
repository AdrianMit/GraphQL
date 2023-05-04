<?php

namespace Dreamlabs\Tests\DataProvider;


use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class TestExtendedType extends AbstractObjectType
{
    public function build(ObjectTypeConfig $config): void
    {
        $config->applyInterface(new TestInterfaceType())
            ->addField('ownField', new StringType());
    }


}
