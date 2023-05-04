<?php

namespace Dreamlabs\Tests\Library\Relay;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Relay\Connection\Connection;
use Dreamlabs\GraphQL\Relay\Type\PageInfoType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\Tests\DataProvider\TestObjectType;

class ConnectionTest extends TestCase
{

    public function testConnectionArgs(): void
    {
        $this->assertEquals([
            'after'  => ['type' => TypeMap::TYPE_STRING],
            'first'  => ['type' => TypeMap::TYPE_INT],
            'before' => ['type' => TypeMap::TYPE_STRING],
            'last'   => ['type' => TypeMap::TYPE_INT],

        ], Connection::connectionArgs());
    }

    public function testPageInfoType(): void
    {
        $type = new PageInfoType();
        $this->assertEquals('PageInfo', $type->getName());
        $this->assertEquals('Information about pagination in a connection.', $type->getDescription());
        $this->assertTrue($type->hasField('hasNextPage'));
        $this->assertTrue($type->hasField('hasPreviousPage'));
        $this->assertTrue($type->hasField('startCursor'));
        $this->assertTrue($type->hasField('endCursor'));
    }

    public function testEdgeDefinition(): void
    {
        $edgeType = Connection::edgeDefinition(new StringType(), 'user');
        $this->assertEquals('userEdge', $edgeType->getName());
        $this->assertTrue($edgeType->hasField('node'));
        $this->assertTrue($edgeType->hasField('cursor'));
    }

    public function testConnectionDefinition(): void
    {
        $connection = Connection::connectionDefinition(new TestObjectType(), 'user');
        $this->assertEquals($connection->getName(), 'userConnection');
        $this->assertTrue($connection->hasField('pageInfo'));
        $this->assertTrue($connection->hasField('edges'));
    }

}
