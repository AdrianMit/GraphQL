<?php

namespace Dreamlabs\Tests\Library\Type;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Type\ListType\ListType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\Tests\DataProvider\TestListType;


class ListTypeTest extends TestCase
{

    public function testInline(): void
    {
        $listType = new ListType(new StringType());
        $this->assertEquals(new StringType(), $listType->getNamedType());
        $this->assertEquals(new StringType(), $listType->getTypeOf());
        $this->assertTrue($listType->isCompositeType());
        $this->assertTrue($listType->isValidValue(['Test', 'Value']));
        $this->assertFalse($listType->isValidValue('invalid value'));
    }

    public function testStandaloneClass(): void
    {
        $listType = new TestListType();
        $this->assertEquals(new StringType(), $listType->getNamedType());
    }

    public function testListOfInputsWithArguments(): void
    {

    }

}
