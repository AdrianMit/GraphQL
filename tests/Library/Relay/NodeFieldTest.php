<?php

namespace Dreamlabs\Tests\Library\Relay;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Relay\Fetcher\CallableFetcher;
use Dreamlabs\GraphQL\Relay\Field\NodeField;

class NodeFieldTest extends TestCase
{

    public function testMethods(): void
    {
        $fetcher = new CallableFetcher(function (): void { }, function (): void { });
        $field   = new NodeField($fetcher);

        $this->assertEquals('Fetches an object given its ID', $field->getDescription());
        $this->assertEquals('node', $field->getName());
        $this->assertEquals($fetcher, $field->getType()->getFetcher());
    }
}
