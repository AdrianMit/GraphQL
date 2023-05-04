<?php

namespace Dreamlabs\Tests\Library\Type;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\Tests\DataProvider\TestTimeType;

class ScalarExtendTypeTest extends TestCase
{

    public function testType(): void
    {
        $reportType = new ObjectType([
            'name'   => 'Report',
            'fields' => [
                'time'  => new TestTimeType(),
                'title' => new StringType(),
            ]
        ]);
        $processor  = new Processor(new Schema([
                'query' => new ObjectType([
                    'name'   => 'RootQueryType',
                    'fields' => [
                        'latestReport' => [
                            'type'    => $reportType,
                            'resolve' => fn(): array => [
                                'title' => 'Accident #1',
                                'time'  => '13:30:12',
                            ]
                        ],
                    ]
                ])
            ])
        );

        $processor->processPayload('{ latestReport { title, time} }');
        $this->assertEquals(['data' => ['latestReport' => ['title' => 'Accident #1', 'time' => '13:30:12']]], $processor->getResponseData());


    }

}
