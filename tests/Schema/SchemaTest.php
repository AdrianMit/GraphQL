<?php

namespace Dreamlabs\Tests\Schema;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\Tests\DataProvider\TestEmptySchema;
use Dreamlabs\Tests\DataProvider\TestObjectType;
use Dreamlabs\Tests\DataProvider\TestSchema;

class SchemaTest extends TestCase
{

    public function testStandaloneEmptySchema(): void
    {
        $schema = new TestEmptySchema();
        $this->assertFalse($schema->getQueryType()->hasFields());
    }

    public function testStandaloneSchema(): void
    {
        $schema = new TestSchema();
        $this->assertTrue($schema->getQueryType()->hasFields());
        $this->assertTrue($schema->getMutationType()->hasFields());

        $this->assertEquals(1, is_countable($schema->getMutationType()->getFields()) ? count($schema->getMutationType()->getFields()) : 0);

        $schema->addMutationField('changeUser', ['type' => new TestObjectType(), 'resolve' => function (): void {
        }]);
        $this->assertEquals(2, is_countable($schema->getMutationType()->getFields()) ? count($schema->getMutationType()->getFields()) : 0);

    }

    public function testSchemaWithoutClosuresSerializable(): void
    {
        $schema = new TestEmptySchema();
        $schema->getQueryType()->addField('randomInt', [
            'type'    => new NonNullType(new IntType()),
            'resolve' => 'rand',
        ]);

        $serialized = serialize($schema);
        /** @var Schema $unserialized */
        $unserialized = unserialize($serialized);

        $this->assertTrue($unserialized->getQueryType()->hasFields());
        $this->assertFalse($unserialized->getMutationType()->hasFields());
        $this->assertEquals(1, is_countable($unserialized->getQueryType()->getFields()) ? count($unserialized->getQueryType()->getFields()) : 0);
    }

    public function testCustomTypes(): void
    {
        $authorType = null;

        $userInterface = new ObjectType([
            'name'        => 'UserInterface',
            'fields'      => [
                'name' => new StringType(),
            ],
            'resolveType' => fn() => $authorType
        ]);

        $authorType = new ObjectType([
            'name'       => 'Author',
            'fields'     => [
                'name' => new StringType(),
            ],
            'interfaces' => [$userInterface]
        ]);

        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'QueryType',
                'fields' => [
                    'user' => [
                        'type'    => $userInterface,
                        'resolve' => fn(): array => [
                            'name' => 'Alex'
                        ]
                    ]
                ]
            ])
        ]);
        $schema->getTypesList()->addType($authorType);
        $processor = new Processor($schema);
        $processor->processPayload('{ user { name } }');
        $this->assertEquals(['data' => ['user' => ['name' => 'Alex']]], $processor->getResponseData());

        $processor->processPayload('{
                    __schema {
                        types {
                            name
                        }
                    }
                }');
        $data = $processor->getResponseData();
        $this->assertArraySubset([11 => ['name' => 'Author']], $data['data']['__schema']['types']);

        $processor->processPayload('{ user { name { } } }');
        $result = $processor->getResponseData();

        $this->assertEquals(['errors' => [[
            'message'   => 'Unexpected token "RBRACE"',
            'locations' => [
                [
                    'line'   => 1,
                    'column' => 19
                ]
            ]
        ]]], $result);
        $processor->getExecutionContext()->clearErrors();

        $processor->processPayload('{ user { name { invalidSelection } } }');
        $result = $processor->getResponseData();

        $this->assertEquals(['data' => ['user' => null], 'errors' => [[
            'message'   => 'You can\'t specify fields for scalar type "String"',
            'locations' => [
                [
                    'line'   => 1,
                    'column' => 10
                ]
            ]
        ]]], $result);
    }

}
