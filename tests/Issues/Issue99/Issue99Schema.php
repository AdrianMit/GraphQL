<?php
namespace Dreamlabs\Tests\Issues\Issue99;

use Dreamlabs\GraphQL\Config\Schema\SchemaConfig;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Schema\AbstractSchema;
use Dreamlabs\GraphQL\Type\InputObject\InputObjectType;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class Issue99Schema extends AbstractSchema
{
    public function build(SchemaConfig $config): void
    {
        $config->setQuery(
            new ObjectType([
                'fields' => [
                    new Field([
                        'name' => 'items',
                        'type' => new ListType(new ObjectType([
                            'fields'  => [
                                'id'   => new NonNullType(new IdType()),
                                new Field([
                                    'name' => 'custom',
                                    'type' => new ObjectType([
                                        'fields' => [
                                            'value' => new StringType()
                                        ],
                                    ]),
                                    'args' => [
                                        'argX' => [
                                            'type' => new NonNullType(new InputObjectType([
                                                'fields' => [
                                                    'x' => new NonNullType(new StringType())
                                                ]
                                            ]))
                                        ]
                                    ],
                                    'resolve' => function($source, $args) {
                                        $x = $args['argX']['x'] ?? Issue99Test::BUG_EXISTS_VALUE;

                                        return [
                                            'value' => $x
                                        ];
                                    }
                                ])
                            ],
                        ])),
                        'args'    => [
                            'example' => new StringType()
                        ],
                        'resolve' => fn(): array => [
                            ['id' => 1],
                            ['id' => 2],
                            ['id' => 3],
                            ['id' => 4],
                        ]
                    ])
                ]
            ])
        );
    }
}
