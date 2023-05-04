<?php
namespace Dreamlabs\Tests\Library\Relay;

use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Relay\Fetcher\CallableFetcher;
use Dreamlabs\GraphQL\Relay\NodeInterfaceType;
use Dreamlabs\Tests\DataProvider\TestObjectType;

class NodeInterfaceTypeTest extends TestCase
{

    public function testMethods(): void
    {
        $type       = new NodeInterfaceType();
        $testObject = new TestObjectType();


        $this->assertEquals('NodeInterface', $type->getName());
        $this->assertNull($type->getFetcher());
        $this->assertNull($type->resolveType($testObject));

        $fetcher = new CallableFetcher(function (): void { }, fn(): TestObjectType => new TestObjectType());
        $type->setFetcher($fetcher);
        $this->assertEquals($fetcher, $type->getFetcher());

        $this->assertEquals($testObject, $type->resolveType($testObject));
    }

}
