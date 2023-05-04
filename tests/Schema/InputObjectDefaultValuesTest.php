<?php

namespace Dreamlabs\Tests\Schema;

use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\Enum\EnumType;
use Dreamlabs\GraphQL\Type\InputObject\InputObjectType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\DateTimeType;
use Dreamlabs\GraphQL\Type\Scalar\DateTimeTzType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class InputObjectDefaultValuesTest extends TestCase
{

    public function testDefaultEnum(): void
    {
        $enumType = new EnumType([
            'name'   => 'InternalStatus',
            'values' => [
                [
                    'name'  => 'ACTIVE',
                    'value' => 1,
                ],
                [
                    'name'  => 'DISABLED',
                    'value' => 0,
                ],
            ]
        ]);
        $schema   = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'stringQuery' => [
                        'type'       => new StringType(),
                        'args'       => [
                            'statObject' => new InputObjectType([
                                'name'   => 'StatObjectType',
                                'fields' => [
                                    'status' => [
                                        'type'    => $enumType,
                                        'defaultValue' => 1
                                    ],
                                    'level'  => new NonNullType(new IntType())
                                ]
                            ])
                        ],
                        'resolve'    => fn($source, $args): string => sprintf('Result with level %s and status %s',
                            $args['statObject']['level'], $args['statObject']['status']
                        ),
                    ],
                    'enumObject' => [
                        'type' => new ObjectType([
                            'name'   => 'EnumObject',
                            'fields' => [
                                'status' => $enumType
                            ]
                        ]),
                        'resolve' => fn(): array => [
                            'status' => null
                        ]
                    ],

                ]
            ])
        ]);

        $processor = new Processor($schema);
        $processor->processPayload('{ stringQuery(statObject: { level: 1 }) }');
        $result = $processor->getResponseData();
        $this->assertEquals(['data' => [
            'stringQuery' => 'Result with level 1 and status 1'
        ]], $result);

        $processor->processPayload('{ stringQuery(statObject: { level: 1, status: DISABLED }) }');
        $result = $processor->getResponseData();

        $this->assertEquals(['data' => [
            'stringQuery' => 'Result with level 1 and status 0'
        ]], $result);

        $processor->processPayload('{ enumObject { status } }');
        $result = $processor->getResponseData();

        $this->assertEquals(['data' => [
            'enumObject' => [
                'status' => null
            ]
        ]], $result);
    }

}
