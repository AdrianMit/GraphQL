<?php

namespace Dreamlabs\Tests\Library\Type;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Parser\Ast\ArgumentValue\InputObject;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\InputObject\InputObjectType;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\BooleanType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\Tests\DataProvider\TestInputObjectType;

class InputObjectTypeTest extends TestCase
{

    public function testInternal(): void
    {
        $inputObjectType = new InputObjectType([
            'name'   => 'PostData',
            'fields' => [
                'title' => new NonNullType(new StringType()),
            ]
        ]);
        $this->assertEquals(TypeMap::KIND_INPUT_OBJECT, $inputObjectType->getKind());
        $this->assertEquals('PostData', $inputObjectType->getName());

        $this->assertFalse($inputObjectType->isValidValue('invalid value'));
        $this->assertTrue($inputObjectType->isValidValue(['title' => 'Super ball!']));
        $this->assertFalse($inputObjectType->isValidValue(['title' => null]));
    }

    public function testStandaloneClass(): void
    {
        $inputObjectType = new TestInputObjectType();
        $this->assertEquals('TestInputObject', $inputObjectType->getName());
    }

    public function testListOfInputWithNonNull(): void
    {
        $processor = new Processor(new Schema([
            'query'    => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'empty' => [
                        'type'    => new StringType(),
                        'resolve' => fn() => null
                    ]
                ]
            ]),
            'mutation' => new ObjectType([
                'name'   => 'RootMutation',
                'fields' => [
                    'createList' => [
                        'args'    => [
                            'posts' => new ListType(new InputObjectType([
                                'name'   => 'PostInputType',
                                'fields' => [
                                    'title' => new NonNullType(new StringType()),
                                ]
                            ]))
                        ],
                        'type'    => new BooleanType(),
                        'resolve' => fn($object, $args): bool => true
                    ]
                ]
            ])
        ]));

        $processor->processPayload('mutation { createList(posts: [{title: null }, {}]) }');
        $this->assertEquals(
            [
                'data'   => ['createList' => null],
                'errors' => [[
                    'message'   => 'Not valid type for argument "posts" in query "createList": Not valid type for field "title" in input type "PostInputType": Field must not be NULL',
                    'locations' => [
                        [
                            'line'   => 1,
                            'column' => 23
                        ]
                    ]
                ]]
            ],
            $processor->getResponseData()
        );
    }

    public function testNullableInputWithNonNull(): void
    {
        $processor = new Processor(new Schema([
            'query'    => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'empty' => [
                        'type'    => new StringType(),
                        'resolve' => fn() => null
                    ]
                ]
            ]),
            'mutation' => new ObjectType([
                'name'   => 'RootMutation',
                'fields' => [
                    'createAuthor' => [
                        'args'    => [
                            'author' => new InputObjectType([
                                'name'   => 'AuthorInputType',
                                'fields' => [
                                    'name' => new NonNullType(new StringType()),
                                ]
                            ])
                        ],
                        'type'    => new BooleanType(),
                        'resolve' => fn($object, $args): bool => true
                    ]
                ]
            ])
        ]));
        $processor->processPayload('mutation { createAuthor(author: null) }');
        $this->assertEquals(
            [
                'data' => ['createAuthor' => true],
            ],
            $processor->getResponseData()
        );
    }

    public function testListInsideInputObject(): void
    {
        $processor = new Processor(new Schema([
            'query'    => new ObjectType([
                'name'   => 'RootQueryType',
                'fields' => [
                    'empty' => [
                        'type'    => new StringType(),
                        'resolve' => function (): void {
                        }
                    ],
                ]
            ]),
            'mutation' => new ObjectType([
                'name'   => 'RootMutation',
                'fields' => [
                    'createList' => [
                        'type'    => new StringType(),
                        'args'    => [
                            'topArgument' => new InputObjectType([
                                'name'   => 'topArgument',
                                'fields' => [
                                    'postObject' => new ListType(new InputObjectType([
                                        'name'   => 'postObject',
                                        'fields' => [
                                            'title' => new NonNullType(new StringType()),
                                        ]
                                    ]))
                                ]
                            ])
                        ],
                        'resolve' => fn(): string => 'success message'
                    ]
                ]
            ])
        ]));
        $processor->processPayload('mutation { createList(topArgument: { postObject:[ { title: null } ] })}');
        $this->assertEquals([
            'data'   => ['createList' => null],
            'errors' => [[
                'message'   => 'Not valid type for argument "topArgument" in query "createList": Not valid type for field "postObject" in input type "topArgument": Not valid type for field "title" in input type "postObject": Field must not be NULL',
                'locations' => [
                    [
                        'line'   => 1,
                        'column' => 23
                    ]
                ]
            ]],
        ], $processor->getResponseData());
        $processor->getExecutionContext()->clearErrors();
        $processor->processPayload('mutation { createList(topArgument:{
                                        postObject:[{title: "not empty"}] })}');
        $this->assertEquals(['data' => ['createList' => 'success message']], $processor->getResponseData());
    }

    public function testInputObjectDefaultValue(): void
    {
        $processor = new Processor(new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'cities' => [
                        'type'    => new ListType(new StringType()),
                        'args'    => [
                            'paging' => [
                                'type'         => new InputObjectType([
                                    'name'   => 'paging',
                                    'fields' => [
                                        'limit'  => new IntType(),
                                        'offset' => new IntType(),
                                    ]
                                ]),
                                'defaultValue' => [
                                    'limit'  => 10,
                                    'offset' => 0,
                                ],
                            ],
                        ],
                        'resolve' => fn($source, $args): array => [
                            'limit is ' . $args['paging']['limit'],
                            'offset is ' . $args['paging']['offset'],
                        ]
                    ],

                ]
            ]),
        ]));
        $processor->processPayload('{ cities }');
        $response = $processor->getResponseData();
        $this->assertEquals(
            [
                'data' => ['cities' => [
                    'limit is 10',
                    'offset is 0'
                ]],
            ],
            $response
        );
    }

    public function testInvalidTypeErrors(): void
    {
        $processor = new Processor(new Schema([
            'query' => new ObjectType([
                'name' => 'RootQuery',
                'fields' => [
                    'hello' => new StringType(),
                ]
            ]),
            'mutation' => new ObjectType([
                'name'   => 'RootMutation',
                'fields' => [
                    'createPost' => [
                        'type'    => new StringType(),
                        'args'    => [
                            'object' => new InputObjectType([
                                'name'   => 'InputPostType',
                                'fields' => [
                                    'title'  => new NonNullType(new StringType()),
                                    'userId' => new NonNullType(new IntType()),
                                ]
                            ]),
                        ],
                        'resolve' => fn($source, $args): string => sprintf('%s by %s', $args['title'], $args['userId'])
                    ],

                ]
            ]),
        ]));
        $processor->processPayload('mutation { createPost(object: {title: "Hello world"}) }');
        $response = $processor->getResponseData();
        $this->assertEquals(
            [
                'data' => ['createPost' => null],
                'errors' => [[
                    'message' => 'Not valid type for argument "object" in query "createPost": userId is required on InputPostType',
                    'locations' => [
                        ['line' => 1, 'column' => 23]
                    ]
                ]]
            ],
            $response
        );
    }


}
