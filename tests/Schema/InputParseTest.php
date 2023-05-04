<?php

namespace Dreamlabs\Tests\Schema;

use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\DateTimeType;
use Dreamlabs\GraphQL\Type\Scalar\DateTimeTzType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class InputParseTest extends TestCase
{

    /**
     * @dataProvider queries
     *
     * @param $query
     * @param $expected
     */
    public function testDateInput($query, $expected): void
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'stringQuery' => [
                        'type'    => new StringType(),
                        'args'    => [
                            'from'   => new DateTimeType('Y-m-d H:i:s'),
                            'fromtz' => new DateTimeTzType(),
                        ],
                        'resolve' => fn($source, $args): string => sprintf('Result with %s date and %s tz',
                            empty($args['from']) ? 'default' : $args['from']->format('Y-m-d H:i:s'),
                            empty($args['fromtz']) ? 'default' : $args['fromtz']->format('r')
                        ),
                    ],
                ]
            ])
        ]);

        $processor = new Processor($schema);
        $processor->processPayload($query);
        $result = $processor->getResponseData();

        $this->assertEquals($expected, $result);
    }

    public function queries()
    {
        return [
            [
                '{
                  stringQuery(fromtz: "Mon, 14 Nov 2016 04:48:13 +0000")
                }',
                [
                    'data' => [
                        'stringQuery' => 'Result with default date and Mon, 14 Nov 2016 04:48:13 +0000 tz'
                    ],
                ]
            ],
            [
                '{
                  stringQuery(from: "2016-10-30 06:10:22")
                }',
                [
                    'data' => [
                        'stringQuery' => 'Result with 2016-10-30 06:10:22 date and default tz'
                    ],
                ]
            ],
        ];
    }

}
