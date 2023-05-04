<?php

namespace Dreamlabs\Tests\Schema;

use Stringable;
use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class uid implements Stringable
{
    public function __construct(private $uid)
    {
    }

    public function __toString(): string
    {
        return (string) $this->uid;
    }
}

class NonNullableTest extends TestCase
{

    /**
     * @dataProvider queries
     *
     * @param $query
     * @param $expected
     */
    public function testNullableResolving($query, $expected): void
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'nonNullScalar'        => [
                        'type'    => new NonNullType(new IntType()),
                        'resolve' => fn() => null,
                    ],
                    'nonNullList'          => [
                        'type'    => new NonNullType(new ListType(new IntType())),
                        'resolve' => fn() => null
                    ],
                    'user'                 => [
                        'type'    => new NonNullType(new ObjectType([
                            'name'   => 'User',
                            'fields' => [
                                'id'   => new NonNullType(new IdType()),
                                'name' => new StringType(),
                            ]
                        ])),
                        'resolve' => fn(): array => [
                            'id'   => new uid('6cfb044c-9c0a-4ddd-9ef8-a0b940818db3'),
                            'name' => 'Alex'
                        ]
                    ],
                    'nonNullListOfNpnNull' => [
                        'type'    => new NonNullType(new ListType(new NonNullType(new IntType()))),
                        'resolve' => fn(): array => [1, null]
                    ],
                    'nonNullArgument'     => [
                        'args'    => [
                            'ids' => new NonNullType(new ListType(new IntType()))
                        ],
                        'type'    => new IntType(),
                        'resolve' => fn(): int => 1
                    ],
                    'nonNullArgument2'     => [
                        'args'    => [
                            'ids' => new NonNullType(new ListType(new NonNullType(new IntType())))
                        ],
                        'type'    => new IntType(),
                        'resolve' => fn(): int => 1
                    ],
                ]
            ])
        ]);

        $processor = new Processor($schema);
        $processor->processPayload($query);
        $result = $processor->getResponseData();

        $this->assertEquals($expected, $result);
    }

    public function queries()
    {
        return [
            [
                '{ test:nonNullArgument2(ids: [1, 2]) }',
                [
                    'data' => [
                        'test' => 1
                    ]
                ],
            ],
            [
                '{ test:nonNullArgument2(ids: [1, null]) }',
                [
                    'data' => [
                        'test' => null
                    ],
                    'errors' => [
                        [
                            'message' => 'Not valid type for argument "ids" in query "nonNullArgument2": Field must not be NULL',
                            'locations' => [['line' => 1, 'column' => 25]]
                        ]
                    ]
                ],
            ],
            [
                '{ test:nonNullArgument(ids: [1, null]) }',
                [
                    'data' => [
                        'test' => 1
                    ]
                ]
            ],
            [
                '{ test:nonNullArgument }',
                [
                    'data' => [
                        'test' => null
                    ],
                    'errors' => [
                        [
                            'message' => 'Require "ids" arguments to query "nonNullArgument"'
                        ]
                    ]
                ]
            ],
            [
                '{ nonNullScalar  }',
                [
                    'data'   => [
                        'nonNullScalar' => null
                    ],
                    'errors' => [
                        [
                            'message' => 'Cannot return null for non-nullable field "nonNullScalar"'
                        ]
                    ]
                ]
            ],

            [
                '{ nonNullList  }',
                [
                    'data'   => [
                        'nonNullList' => null
                    ],
                    'errors' => [
                        [
                            'message' => 'Cannot return null for non-nullable field "nonNullList"'
                        ]
                    ]
                ]
            ],
            [
                '{ nonNullListOfNpnNull  }',
                [
                    'data'   => [
                        'nonNullListOfNpnNull' => null,
                    ],
                    'errors' => [
                        [
                            'message' => 'Not valid resolved type for field "nonNullListOfNpnNull": Field must not be NULL'
                        ]
                    ]
                ]
            ],

            [
                '{ user {id, name}  }',
                [
                    'data' => [
                        'user' => [
                            'id'   => '6cfb044c-9c0a-4ddd-9ef8-a0b940818db3',
                            'name' => 'Alex'
                        ]
                    ]
                ]
            ],
            [
                '{ user { __typename }  }',
                [
                    'data' => [
                        'user' => [
                            '__typename' => 'User'
                        ]
                    ]
                ]
            ]
        ];
    }

}
