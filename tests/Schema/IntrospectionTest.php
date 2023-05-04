<?php

namespace Dreamlabs\Tests\Schema;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Directive\Directive;
use Dreamlabs\GraphQL\Directive\DirectiveLocation;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Field\InputField;
use Dreamlabs\GraphQL\Type\Enum\EnumType;
use Dreamlabs\GraphQL\Type\InterfaceType\InterfaceType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\BooleanType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\GraphQL\Type\Union\UnionType;
use Dreamlabs\Tests\DataProvider\TestEmptySchema;
use Dreamlabs\Tests\DataProvider\TestSchema;

class IntrospectionTest extends TestCase
{
    private string $introspectionQuery = <<<TEXT
query IntrospectionQuery {
                __schema {
                    queryType { name }
                    mutationType { name }
                    types {
                        ...FullType
                    }
                    directives {
                        name
                        description
                        locations
                        args {
                            ...InputValue
                        }
                    }
                }
            }

            fragment FullType on __Type {
                kind
                name
                description
                fields {
                    name
                    description
                    args {
                        ...InputValue
                    }
                    type {
                        ...TypeRef
                    }
                    isDeprecated
                    deprecationReason
                }
                inputFields {
                    ...InputValue
                }
                interfaces {
                    ...TypeRef
                }
                enumValues {
                    name
                    description
                    isDeprecated
                    deprecationReason
                }
                possibleTypes {
                    ...TypeRef
                }
            }

            fragment InputValue on __InputValue {
                name
                description
                type { ...TypeRef }
                defaultValue
            }

            fragment TypeRef on __Type {
                kind
                name
                ofType {
                    kind
                    name
                    ofType {
                        kind
                        name
                        ofType {
                            kind
                            name
                        }
                    }
                }
            }
TEXT;


    public function testIntrospectionDirectiveRequest(): void
    {
        $processor = new Processor(new TestSchema());

        $processor->processPayload($this->introspectionQuery, []);

        $this->assertTrue(is_array($processor->getResponseData()));
    }

    /**
     * @param $query
     * @param $expectedResponse
     *
     * @dataProvider predefinedSchemaProvider
     */
    public function testPredefinedQueries($query, $expectedResponse): void
    {
        $schema = new TestEmptySchema();
        $schema->addQueryField(new Field([
            'name'              => 'latest',
            'type'              => new ObjectType([
                'name'   => 'LatestType',
                'fields' => [
                    'id'   => ['type' => TypeMap::TYPE_INT],
                    'name' => ['type' => TypeMap::TYPE_STRING]
                ],
            ]),
            'args'              => [
                'id' => ['type' => TypeMap::TYPE_INT, 'defaultValue' => 'test'],
                'id2' => ['type' => TypeMap::TYPE_INT]
            ],
            'description'       => 'latest description',
            'deprecationReason' => 'for test',
            'isDeprecated'      => true,
            'resolve'           => fn(): array => [
                'id'   => 1,
                'name' => 'Alex'
            ]
        ]));

        $processor = new Processor($schema);

        $processor->processPayload($query);
        $responseData = $processor->getResponseData();

        $this->assertEquals($expectedResponse, $responseData);
    }

    public function predefinedSchemaProvider()
    {
        return [
            [
                '{ __type { name } }',
                [
                    'data'   => ['__type' => null],
                    'errors' => [['message' => 'Require "name" arguments to query "__type"']]
                ]
            ],
            [
                '{ __type (name: "__Type") { name } }',
                [
                    'data' => [
                        '__type' => ['name' => '__Type']
                    ]
                ]
            ],
            [
                '{ __type (name: "InvalidName") { name } }',
                [
                    'data' => [
                        '__type' => null
                    ]
                ]
            ],
            [
                '{
                    __schema {
                        types {
                            name,
                            fields (includeDeprecated: true) {
                                name
                                args {
                                    defaultValue
                                }
                            }
                        }
                    }
                }',
                [
                    'data' => [
                        '__schema' => [
                            'types' => [
                                ['name' => 'TestSchemaQuery', 'fields' => [['name' => 'latest', 'args' => [['defaultValue' => 'test'], ['defaultValue' => null]]]]],
                                ['name' => 'Int', 'fields' => null],
                                ['name' => 'LatestType', 'fields' => [['name' => 'id', 'args' => []], ['name' => 'name', 'args' => []]]],
                                ['name' => 'String', 'fields' => null],
                                ['name' => '__Schema', 'fields' => [['name' => 'queryType', 'args' => []], ['name' => 'mutationType', 'args' => []], ['name' => 'subscriptionType', 'args' => []], ['name' => 'types', 'args' => []], ['name' => 'directives', 'args' => []]]],
                                ['name' => '__Type', 'fields' => [['name' => 'name', 'args' => []], ['name' => 'kind', 'args' => []], ['name' => 'description', 'args' => []], ['name' => 'ofType', 'args' => []], ['name' => 'inputFields', 'args' => []], ['name' => 'enumValues', 'args' => [['defaultValue' => 'false']]], ['name' => 'fields', 'args' => [['defaultValue' => 'false']]], ['name' => 'interfaces', 'args' => []], ['name' => 'possibleTypes', 'args' => []]]],
                                ['name' => '__InputValue', 'fields' => [['name' => 'name', 'args' => []], ['name' => 'description', 'args' => []], ['name' => 'isDeprecated', 'args' => []], ['name' => 'deprecationReason', 'args' => []], ['name' => 'type', 'args' => []], ['name' => 'defaultValue', 'args' => []],]],
                                ['name' => 'Boolean', 'fields' => null],
                                ['name' => '__EnumValue', 'fields' => [['name' => 'name', 'args' => []], ['name' => 'description', 'args' => []], ['name' => 'deprecationReason', 'args' => []], ['name' => 'isDeprecated', 'args' => []],]],
                                ['name' => '__Field', 'fields' => [['name' => 'name', 'args' => []], ['name' => 'description', 'args' => []], ['name' => 'isDeprecated', 'args' => []], ['name' => 'deprecationReason', 'args' => []], ['name' => 'type', 'args' => []], ['name' => 'args', 'args' => []]]],
                                ['name' => '__Directive', 'fields' => [['name' => 'name', 'args' => []], ['name' => 'description', 'args' => []], ['name' => 'args', 'args' => []], ['name' => 'locations', 'args' => []]]],
                                ['name' => '__DirectiveLocation', 'fields' => null],
                            ]
                        ]
                    ]
                ]
            ],
            [
                '{
                  test : __schema {
                    queryType {
                      kind,
                      name,
                      fields (includeDeprecated: true) {
                        name,
                        isDeprecated,
                        deprecationReason,
                        description,
                        type {
                          name
                        }
                      }
                    }
                  }
                }',
                ['data' => [
                    'test' => [
                        'queryType' => [
                            'name'   => 'TestSchemaQuery',
                            'kind'   => 'OBJECT',
                            'fields' => [
                                ['name' => 'latest', 'isDeprecated' => true, 'deprecationReason' => 'for test', 'description' => 'latest description', 'type' => ['name' => 'LatestType']]
                            ]
                        ]
                    ]
                ]]
            ],
            [
                '{
                  __schema {
                    queryType {
                      kind,
                      name,
                      description,
                      interfaces {
                        name
                      },
                      possibleTypes {
                        name
                      },
                      inputFields {
                        name
                      },
                      ofType{
                        name
                      }
                    }
                  }
                }',
                ['data' => [
                    '__schema' => [
                        'queryType' => [
                            'kind'          => 'OBJECT',
                            'name'          => 'TestSchemaQuery',
                            'description'   => null,
                            'interfaces'    => [],
                            'possibleTypes' => null,
                            'inputFields'   => null,
                            'ofType'        => null
                        ]
                    ]
                ]]
            ]
        ];
    }

    public function testCombinedFields(): void
    {
        $schema = new TestEmptySchema();

        $interface = new InterfaceType([
            'name'        => 'TestInterface',
            'fields'      => [
                'id'   => ['type' => new IntType()],
                'name' => ['type' => new IntType()],
            ],
            'resolveType' => function ($type): void {

            }
        ]);

        $object1 = new ObjectType([
            'name'       => 'Test1',
            'fields'     => [
                'id'       => ['type' => new IntType()],
                'name'     => ['type' => new IntType()],
                'lastName' => ['type' => new IntType()],
            ],
            'interfaces' => [$interface]
        ]);

        $object2 = new ObjectType([
            'name'       => 'Test2',
            'fields'     => [
                'id'        => ['type' => new IntType()],
                'name'      => ['type' => new IntType()],
                'thirdName' => ['type' => new IntType()],
            ],
            'interfaces' => [$interface]
        ]);

        $unionType = new UnionType([
            'name'        => 'UnionType',
            'types'       => [$object1, $object2],
            'resolveType' => function (): void {

            }
        ]);

        $schema->addQueryField(new Field([
            'name'    => 'union',
            'type'    => $unionType,
            'args'    => [
                'id' => ['type' => TypeMap::TYPE_INT]
            ],
            'resolve' => fn(): array => [
                'id'   => 1,
                'name' => 'Alex'
            ]
        ]));

        $schema->addMutationField(new Field([
            'name'    => 'mutation',
            'type'    => $unionType,
            'args'    => [
                'type' => new EnumType([
                    'name'   => 'MutationType',
                    'values' => [
                        [
                            'name'  => 'Type1',
                            'value' => 'type_1'
                        ],
                        [
                            'name'  => 'Type2',
                            'value' => 'type_2'
                        ]
                    ]
                ])
            ],
            'resolve' => fn() => null
        ]));

        $processor = new Processor($schema);

        $processor->processPayload($this->introspectionQuery);
        $responseData = $processor->getResponseData();

        /** strange that this test got broken after I fixed the field resolve behavior */
        $this->assertArrayNotHasKey('errors', $responseData);
    }

    public function testCanIntrospectDirectives(): void
    {
        $schema = new TestSchema();
        $schema->getDirectiveList()->addDirectives([
            new Directive([
                'name' => 'skip',
                'args' => [
                    new InputField([
                        'name' => 'if',
                        'type' => new NonNullType(new BooleanType()),
                        'description' => 'Skipped when true.',
                    ])
                ],
                'description' => 'skip',
                'locations' => [
                    DirectiveLocation::FIELD,
                    DirectiveLocation::FRAGMENT_SPREAD,
                    DirectiveLocation::INLINE_FRAGMENT
                ]
            ]),
            new Directive([
                'name' => 'include',
                'args' => [
                    new InputField([
                        'name' => 'if',
                        'type' => new NonNullType(new BooleanType()),
                        'description' => 'Included when true.',
                    ])
                ],
                'description' => 'include',
                'locations' => [
                    DirectiveLocation::FIELD,
                    DirectiveLocation::FRAGMENT_SPREAD,
                    DirectiveLocation::INLINE_FRAGMENT
                ]
            ]),
            new Directive([
                'name' => 'deprecated',
                'args' => [
                    new InputField([
                        'name' => 'reason',
                        'type' => 'string',
                        'description' => 'Explains why this element was deprecated, usually also including a suggestion for how to access supported similar data. Formatted in [Markdown](https://daringfireball.net/projects/markdown/).',
                    ])
                ],
                'description' => 'deprecated',
                'locations' => [
                    DirectiveLocation::FIELD_DEFINITION,
                    DirectiveLocation::ENUM_VALUE
                ]
            ])
        ]);
        $processor = new Processor($schema);

        $processor->processPayload($this->introspectionQuery);
        $responseData = $processor->getResponseData();
        $this->assertArrayNotHasKey('errors', $responseData);
    }

}
