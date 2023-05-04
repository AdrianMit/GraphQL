<?php

namespace Dreamlabs\Tests\DataProvider;


use Dreamlabs\GraphQL\Type\InputObject\AbstractInputObjectType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class TestInputObjectType extends AbstractInputObjectType
{
    public function build($config): void
    {
        $config->addField('name', new NonNullType(new StringType()));
    }

}
