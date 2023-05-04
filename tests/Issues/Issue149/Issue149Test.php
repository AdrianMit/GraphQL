<?php

namespace Dreamlabs\Tests\Issues\Issue116Test;

use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class Issue149Test extends TestCase
{
    public function testInternalVariableArgument(): void
    {
        $schema    = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => [
                        'type'    => new ObjectType([
                            'name'   => 'User',
                            'fields' => [
                                'id'      => new IdType(),
                                'name'    => new StringType(),
                                'age'     => new IntType(),
                                'friends' => new ListType(new ObjectType([
                                    'name'   => 'UserFriend',
                                    'fields' => [
                                        'id'   => new IdType(),
                                        'name' => new StringType(),
                                        'age'  => new IntType(),
                                    ],
                                ])),
                            ],
                        ]),
                        'resolve' => fn(): array => [
                            'id'      => 1,
                            'name'    => 'John',
                            'age'     => 30,
                            'friends' => [
                                [
                                    'id'   => 2,
                                    'name' => 'Friend 1',
                                    'age'  => 31,
                                ],
                                [
                                    'id'   => 3,
                                    'name' => 'Friend 2',
                                    'age'  => 32,
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);
        $processor = new Processor($schema);
        $response  = $processor->processPayload('
{
    user {
        id
        name
        friends {
            id
            name
        }
    }
    user {
        id
        age
        friends {
            id
            age
        }
    }
}')->getResponseData();
        $this->assertEquals(['data' => ['user' => [
            'id'   => '1',
            'name' => 'John',
            'age'  => 30,
            'friends' => [
                [
                    'id'   => 2,
                    'name' => 'Friend 1',
                    'age'  => 31,
                ],
                [
                    'id'   => 3,
                    'name' => 'Friend 2',
                    'age'  => 32,
                ],
            ]
        ]]], $response);
    }
}
