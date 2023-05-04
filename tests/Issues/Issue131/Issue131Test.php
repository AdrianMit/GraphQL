<?php

namespace Dreamlabs\Tests\Issues\Issue116Test;

use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\InputObject\InputObjectType;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class Issue131Test extends TestCase
{

    public function testInternalVariableArgument(): void
    {


        $schema    = new Schema([
            'query'    => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'hello' => new StringType(),
                ]
            ]),
            'mutation' => new ObjectType([
                'name'   => 'RootMutation',
                'fields' => [
                    'createMeeting' => [
                        'type'    => new ObjectType([
                            'name'   => 'Meeting',
                            'fields' => [
                                'id'   => new IdType(),
                                'name' => new StringType(),
                            ]
                        ]),
                        'args'    => [
                            'name'          => new StringType(),
                            'related_beans' => new ListType(new ObjectType([
                                'name'   => 'RelatedBeanInputType',
                                'fields' => [
                                    'id'     => new IntType(),
                                    'module' => new StringType(),
                                ]
                            ]))
                        ],
                        'resolve' => fn($source, $args): array => [
                            'id' => '1',
                            'name' => sprintf('Meeting with %d beans', is_countable($args['related_beans']) ? count($args['related_beans']) : 0),
                        ]
                    ]
                ]
            ])
        ]);
        $processor = new Processor($schema);
        $response  = $processor->processPayload('
mutation ($related_beans: RelatedBeanInputType) {
  createMeeting(name: "Meeting 1", related_beans: $related_beans) {
    id,
    name
  }
}',
            [
                "related_beans" => [
                    ["module" => "contacts", "id" => "5cc6be5b-fb86-2671-e2c0-55e749882d29"],
                    ["module" => "contacts", "id" => "2a135003-3765-af3f-bc54-55e7497e77aa"],

                ]
            ])->getResponseData();
        $this->assertEquals(['data' => ['createMeeting' => [
            'id' => '1',
            'name' => 'Meeting with 2 beans'
        ]]], $response);
    }
}
