<?php

namespace Dreamlabs\Tests\Schema;

use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Config\Object\InterfaceTypeConfig;
use Dreamlabs\GraphQL\Config\Object\ObjectTypeConfig;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Dreamlabs\GraphQL\Type\InterfaceType\InterfaceType;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Object\AbstractObjectType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;


class UserType extends AbstractObjectType
{
    public function build(ObjectTypeConfig $config): void
    {
        $config->addFields([
            'id'           => new IdType(),
            'fullName'     => new StringType(),
            'reservations' => new ListType(new ReservationInterface())
        ]);
    }
}

class CourtReservation extends AbstractObjectType
{

    public function build(ObjectTypeConfig $config): void
    {
        $config->addFields([
            'id'      => new IdType(),
            'players' => new ListType(new ObjectType([
                'name'   => 'Player',
                'fields' => [
                    'id'   => new IdType(),
                    'user' => new UserType()
                ]
            ]))
        ]);
    }

    public function getInterfaces()
    {
        return [new ReservationInterface()];
    }

}

class ClassReservation extends AbstractObjectType
{
    public function build(ObjectTypeConfig $config): void
    {
        $config->addFields([
            'id'   => new IdType(),
            'user' => new UserType()
        ]);
    }

    public function getInterfaces()
    {
        return [new ReservationInterface()];
    }
}

class ReservationInterface extends AbstractInterfaceType
{
    public function resolveType($object)
    {
        return !str_contains($object['id'], 'cl') ? new CourtReservation() : new ClassReservation();
    }

    public function build($config): void
    {
        $config->addFields([
            'id' => new IdType()
        ]);
    }

}

class FragmentsTest extends TestCase
{

    /**
     * @dataProvider queries
     *
     * @param $query
     * @param $expected
     * @param $variables
     */
    public function testVariables($query, $expected, $variables): void
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => [
                        'type'    => new UserType(),
                        'resolve' => fn($args): array => [
                            'id'           => 'user-id-1',
                            'fullName'     => 'Alex',
                            'reservations' => [
                                [
                                    'id'   => 'cl-1',
                                    'user' => [
                                        'id'       => 'user-id-2',
                                        'fullName' => 'User class1'
                                    ],
                                ],
                                [
                                    'id'      => 'court-1',
                                    'players' => [
                                        [
                                            'id'   => 'player-id-1',
                                            'user' => [
                                                'id'       => 'user-id-3',
                                                'fullName' => 'User court1'
                                            ]
                                        ]
                                    ]
                                ],
                            ]
                        ],
                    ],
                ]
            ])
        ]);

        $processor = new Processor($schema);
        $processor->processPayload($query, $variables);
        $result = $processor->getResponseData();

        $this->assertEquals($expected, $result);
    }

    public function queries()
    {
        return [
            [
                'query {
                    user {
                        ...fUser
                        reservations {
                            ...fReservation
                        }
                    }
                }
                fragment fReservation on ReservationInterface {
                    id
                    ... on CourtReservation {
                        players {
                            id
                            user {
                                ...fUser
                            }
                        }
                    }
                    ... on ClassReservation {
                        user {
                            ...fUser
                        }
                    }
                }
                fragment fUser on User {
                    id
                    fullName
                }',
                [
                    'data' => [
                        'user' => [
                            'id'           => 'user-id-1',
                            'fullName'     => 'Alex',
                            'reservations' => [
                                [
                                    'id'   => 'cl-1',
                                    'user' => [
                                        'id'       => 'user-id-2',
                                        'fullName' => 'User class1'
                                    ]
                                ],
                                [
                                    'id'      => 'court-1',
                                    'players' => [
                                        [
                                            'id'   => 'player-id-1',
                                            'user' => [
                                                'id'       => 'user-id-3',
                                                'fullName' => 'User court1'
                                            ]
                                        ]
                                    ]
                                ],
                            ]
                        ]
                    ],
                ],
                [
                ]
            ],
        ];
    }

    public function testSimpleFragment(): void
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => [
                        'type'    => new UserType(),
                        'resolve' => fn($args): array => [
                            'id'       => 'user-id-1',
                            'fullName' => 'Alex',
                        ],
                        'args' => [
                            'id' => new IntType(),
                        ]
                    ],
                ]
            ])
        ]);

        $query = '
        {
            User1: user(id: 1) {
                fullName
            }
            User2: user(id: 1) {
                ...Fields
            }
        }
        
        fragment Fields on User {
            fullName
        }';

        $processor = new Processor($schema);
        $processor->processPayload($query);
        $result = $processor->getResponseData();

        $expected = ['data' => ['User1' => ['fullName' => 'Alex'], 'User2' => ['fullName' => 'Alex']]];
        $this->assertEquals($expected, $result);
    }

}
