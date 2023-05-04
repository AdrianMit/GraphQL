<?php

namespace Dreamlabs\Tests\Library\Relay;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Relay\Fetcher\CallableFetcher;
use Dreamlabs\Tests\DataProvider\TestObjectType;

class CallableFetcherTest extends TestCase
{
    public function testMethods(): void
    {
        $fetcher = new CallableFetcher(fn($type, $id): array => ['name' => $type . ' Name', 'id' => $id], fn($object) => $object);
        $this->assertEquals([
            'name' => 'User Name',
            'id'   => 12
        ], $fetcher->resolveNode('User', 12));

        $object = new TestObjectType();
        $this->assertEquals($object, $fetcher->resolveType($object));
    }

}
