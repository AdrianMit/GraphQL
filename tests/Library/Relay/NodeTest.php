<?php

namespace Dreamlabs\Tests\Library\Relay;


use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Dreamlabs\GraphQL\Relay\Node;

class NodeTest extends TestCase
{
    public function testMethods(): void
    {
        $global     = Node::toGlobalId('user', 1);
        $fromGlobal = Node::fromGlobalId($global);

        $this->assertEquals('user', $fromGlobal[0]);
        $this->assertEquals(1, $fromGlobal[1]);
    }

    public function malformedIdProvider()
    {
        return [
            [''],
            [base64_encode('I have no colon')],
            [null],
        ];
    }

    /**
     * @dataProvider malformedIdProvider
     */
    public function testFromGlobalIdThrowsExceptionIfGivenMalformedId($idToCheck): void
    {
        $this->setExpectedException(InvalidArgumentException::class);
        Node::fromGlobalId($idToCheck);
    }
}
