<?php

namespace Dreamlabs\Tests\Library\Relay;

use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Relay\Connection\ArrayConnection;

class ArrayConnectionTest extends TestCase
{
    public function testCursors(): void
    {
        $offset = 3;
        $data   = ['a', 'b', 'c', 'd', 'e'];
        $cursor = ArrayConnection::keyToCursor($offset);

        $this->assertEquals($offset, ArrayConnection::cursorToKey($cursor));
        $this->assertEquals($cursor, ArrayConnection::cursorForObjectInConnection($data, 'd'));
        $this->assertNull(null, ArrayConnection::cursorToKey(null));

        $this->assertEquals($offset, ArrayConnection::cursorToOffsetWithDefault($cursor, 2));
        $this->assertEquals(2, ArrayConnection::cursorToOffsetWithDefault(null, 2));
    }

    public function testConnectionDefinition(): void
    {
        $data  = ['a', 'b', 'c', 'd', 'e'];
        $edges = [];

        foreach ($data as $key => $item) {
            $edges[] = ArrayConnection::edgeForObjectWithIndex($item, $key);
        }

        $this->assertEquals([
            'totalCount' => count($data),
            'edges'      => $edges,
            'pageInfo'   => [
                'startCursor'     => $edges[0]['cursor'],
                'endCursor'       => $edges[count($edges) - 1]['cursor'],
                'hasPreviousPage' => false,
                'hasNextPage'     => false,
            ],
        ], ArrayConnection::connectionFromArray($data));

        $this->assertEquals([
            'totalCount' => count($data),
            'edges'      => array_slice($edges, 0, 2),
            'pageInfo'   => [
                'startCursor'     => $edges[0]['cursor'],
                'endCursor'       => $edges[1]['cursor'],
                'hasPreviousPage' => false,
                'hasNextPage'     => true,
            ],
        ], ArrayConnection::connectionFromArray($data, ['first' => 2, 'last' => 4]));
    }
}
