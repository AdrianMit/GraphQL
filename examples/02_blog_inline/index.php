<?php

namespace BlogTest;

use Dreamlabs\GraphQL\Execution\Processor;
use Dreamlabs\GraphQL\Schema\Schema;
use Dreamlabs\GraphQL\Type\Object\ObjectType;

if(file_exists(__DIR__ . '/../../../../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../../../../vendor/autoload.php';
} else {
    require_once realpath(__DIR__ . '/../..') . '/vendor/autoload.php';
}

require_once __DIR__ . '/inline-schema.php';
/** @var ObjectType $rootQueryType */

$processor = new Processor(new Schema([
    'query' => $rootQueryType
]));
$payload = '{ latestPost { title(truncated: true), summary } }';

$processor->processPayload($payload);
echo json_encode($processor->getResponseData()) . "\n";
