<?php

namespace Dreamlabs\Tests\DataProvider;


use Dreamlabs\GraphQL\Config\Schema\SchemaConfig;
use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Schema\AbstractSchema;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;

class TestSchema extends AbstractSchema
{
    private int $testStatusValue = 0;

    public function build(SchemaConfig $config): void
    {
        $config->getQuery()->addFields([
            'me'     => [
                'type'    => new TestObjectType(),
                'resolve' => fn($value, $args, ResolveInfo $info) => $info->getReturnType()->getData()
            ],
            'status' => [
                'type'    => new TestEnumType(),
                'resolve' => fn(): int => $this->testStatusValue
            ],
        ]);
        $config->getMutation()->addFields([
            'updateStatus' => [
                'type'    => new TestEnumType(),
                'resolve' => fn(): int => $this->testStatusValue,
                'args'    => [
                    'newStatus' => new TestEnumType(),
                    'list' => new ListType(new IntType())
                ]
            ]
        ]);
    }


}
