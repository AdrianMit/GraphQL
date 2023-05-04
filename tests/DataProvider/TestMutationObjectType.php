<?php

namespace Dreamlabs\Tests\DataProvider;


use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Type\Object\AbstractMutationObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class TestMutationObjectType extends AbstractMutationObjectType
{
    public function getOutputType(): StringType
    {
        return new StringType();
    }

    public function build(ObjectTypeConfig $config): void
    {
        $this->addArgument('increment', new IntType());
    }


}
