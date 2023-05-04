<?php
namespace Dreamlabs\Tests\Issues\Issue90;

use Dreamlabs\GraphQL\Config\Schema\SchemaConfig;
use Dreamlabs\GraphQL\Schema\AbstractSchema;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\DateTimeType;

class Issue90Schema extends AbstractSchema
{

    public function build(SchemaConfig $config): void
    {
        $config->setQuery(
            new ObjectType([
                'name'   => 'QueryType',
                'fields' => [
                    'echo' => [
                        'type'    => new DateTimeType('Y-m-d H:ia'),
                        'args'    => [
                            'date' => new DateTimeType('Y-m-d H:ia')
                        ],
                        'resolve' => function ($value, $args, $info) {

                            if (isset($args['date'])) {
                                return $args['date'];
                            }

                            return null;
                        }
                    ]
                ]
            ])
        );

        $config->setMutation(
            new ObjectType([
                'name'   => 'MutationType',
                'fields' => [
                    'echo' => [
                        'type'    => new DateTimeType('Y-m-d H:ia'),
                        'args'    => [
                            'date' => new DateTimeType('Y-m-d H:ia')
                        ],
                        'resolve' => function ($value, $args, $info) {

                            if (isset($args['date'])) {
                                return $args['date'];
                            }

                            return null;
                        }
                    ]
                ]
            ])
        );
    }

}
