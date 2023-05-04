<?php

namespace Dreamlabs\Tests\DataProvider;

use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\GraphQL\Type\NonNullType;

class TestObjectType extends AbstractObjectType
{

    public function build(ObjectTypeConfig $config): void
    {
        $config
            ->addField('id', new IntType())
            ->addField('name', new StringType())
            ->addField('region', new ObjectType([
                'name'   => 'Region',
                'fields' => [
                    'country' => new StringType(),
                    'city'    => new StringType()
                ],
            ]))
            ->addField('location', [
                 'type'    => new ObjectType(
                     [
                         'name'   => 'Location',
                         'fields' => [
                             'address'    => new StringType()
                         ]
                     ]
                 ),
                 'args'    => [
                     'noop' => new IntType()
                 ],
                 'resolve' => fn($value, $args, $info): array => ['address' => '1234 Street']
             ]
            )
            ->addField(
                'echo', [
                    'type'    => new StringType(),
                    'args'    => [
                        'value' => new NonNullType(new StringType())
                    ],
                    'resolve' => fn($value, $args, $info) => $args['value']
                ]
            );
    }

    public function getInterfaces()
    {
        return [new TestInterfaceType()];
    }

    public function getData()
    {
        return [
            'id'   => 1,
            'name' => 'John'
        ];
    }

}
