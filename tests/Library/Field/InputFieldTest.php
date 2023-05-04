<?php

namespace Dreamlabs\Tests\Library\Field;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Field\InputField;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\InputObject\InputObjectType;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Dreamlabs\Tests\DataProvider\TestInputField;

class InputFieldTest extends TestCase
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
                        args {
                            ...InputValue
                        }
                        onOperation
                        onFragment
                        onField
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

    public function testFieldWithInputFieldArgument(): void
    {
        $schema    = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'amount' => [
                        'type' => new IntType(),
                        'args' => [
                            new InputField([
                                'name' => 'input',
                                'type' => new InputObjectType([
                                    'name'   => 'TestInput',
                                    'fields' => [
                                        new InputField(['name' => 'clientMutationId', 'type' => new NonNullType(new StringType())])
                                    ]
                                ])
                            ])
                        ],
                    ]

                ]
            ])
        ]);
        $processor = new Processor($schema);
        $processor->processPayload($this->introspectionQuery);
    }

    public function testInlineInputFieldCreation(): void
    {
        $field = new InputField([
            'name'         => 'id',
            'type'         => 'id',
            'description'  => 'description',
            'defaultValue' => 123
        ]);

        $this->assertEquals('id', $field->getName());
        $this->assertEquals(new IdType(), $field->getType());
        $this->assertEquals('description', $field->getDescription());
        $this->assertSame(123, $field->getDefaultValue());
    }


    public function testObjectInputFieldCreation(): void
    {
        $field = new TestInputField();

        $this->assertEquals('testInput', $field->getName());
        $this->assertEquals('description', $field->getDescription());
        $this->assertEquals(new IntType(), $field->getType());
        $this->assertEquals('default', $field->getDefaultValue());
    }

    public function testListAsInputField(): void
    {
        new InputField([
            'name' => 'test',
            'type' => new ListType(new IntType()),
        ]);
    }

    /**
     * @dataProvider invalidInputFieldProvider
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidInputFieldParams($fieldConfig): void
    {
        $field = new InputField($fieldConfig);
        ConfigValidator::getInstance()->assertValidConfig($field->getConfig());
    }

    public function invalidInputFieldProvider()
    {
        return [
            [
                [
                    'name' => 'id',
                    'type' => 'invalid type'
                ]
            ],
            [
                [
                    'name' => 'id',
                    'type' => new ObjectType([
                        'name'   => 'test',
                        'fields' => [
                            'id' => ['type' => 'int']
                        ]
                    ])
                ]
            ],
        ];
    }
}
