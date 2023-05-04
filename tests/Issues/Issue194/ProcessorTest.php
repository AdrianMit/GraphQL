<?php

namespace Dreamlabs\Tests\Issue194;

use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Execution\ResolveInfo;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class ProcessorTest extends TestCase
{

    public function testNonNullDefaultValue(): void
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'currentUser' => [
                        'type'    => new StringType(),
                        'args'    => [
                            'age' => [
                                'type'         => new IntType(),
                                'defaultValue' => 20,
                            ],
                        ],
                        'resolve' => static fn($source, $args, ResolveInfo $info): string => 'Alex age ' . $args['age'],
                    ],
                ],
            ]),
        ]);

        $processor = new Processor($schema);

        $this->assertEquals(['data' => ['currentUser' => 'Alex age 20']],
            $processor->processPayload('{ currentUser }')->getResponseData());

        $this->assertEquals(['data' => ['currentUser' => 'Alex age 10']],
            $processor->processPayload('{ currentUser(age:10) }')->getResponseData());
    }

    public function testNullDefaultValue(): void
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'currentUser' => [
                        'type'    => new StringType(),
                        'args'    => [
                            'age' => [
                                'type'         => new IntType(),
                                'defaultValue' => null,
                            ],
                        ],
                        'resolve' => static function ($source, $args, ResolveInfo $info) {
                            if ($args['age'] === null) {
                                $args['age'] = 25;
                            }

                            return 'Alex age ' . $args['age'];
                        },
                    ],
                ],
            ]),
        ]);

        $processor = new Processor($schema);

        $this->assertEquals(['data' => ['currentUser' => 'Alex age 25']],
            $processor->processPayload('{ currentUser }')->getResponseData());

        $this->assertEquals(['data' => ['currentUser' => 'Alex age 10']],
            $processor->processPayload('{ currentUser(age:10) }')->getResponseData());
    }
}
