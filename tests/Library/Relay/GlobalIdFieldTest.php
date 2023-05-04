<?php

namespace Dreamlabs\Tests\Library\Relay;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Relay\Field\GlobalIdField;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Scalar\IdType;

class GlobalIdFieldTest extends TestCase
{

    public function testSimpleMethods(): void
    {
        $typeName = 'user';
        $field    = new GlobalIdField($typeName);
        $this->assertEquals('id', $field->getName());
        $this->assertEquals('The ID of an object', $field->getDescription());
        $this->assertEquals(new NonNullType(new IdType()), $field->getType());
    }
}
