<?php

namespace Dreamlabs\Tests\Library\Relay;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Relay\RelayMutation;
use Dreamlabs\GraphQL\Type\Scalar\IdType;
use Dreamlabs\GraphQL\Type\Scalar\IntType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;

class MutationTest extends TestCase
{

    public function testCreation(): void
    {
        $mutation = RelayMutation::buildMutation('ship', [
            'name' => new StringType()
        ],[
            'id' => new IdType(),
            'name' => new StringType()
        ], function($source, $args, $info): void {

        });
        $this->assertEquals('ship', $mutation->getName());
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidType(): void
    {
        RelayMutation::buildMutation('ship', [
            'name' => new StringType()
        ], new IntType(), function($source, $args, $info): void {});

    }

}
