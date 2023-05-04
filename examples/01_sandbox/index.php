<?php

namespace Sandbox;

use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

if(file_exists(__DIR__ . '/../../../../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../../../../vendor/autoload.php';
} else {
    require_once realpath(__DIR__ . '/../..') . '/vendor/autoload.php';
}

$processor = new Processor(new Schema([
    'query' => new ObjectType([
        'name'   => 'RootQueryType',
        'fields' => [
            'currentTime' => [
                'type'    => new StringType(),
                'resolve' => function () {
                    return date('Y-m-d H:ia');
                }
            ]
        ]
    ])
]));

$processor->processPayload('{ currentTime }');
echo json_encode($processor->getResponseData()) . "\n";
