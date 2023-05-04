<?php

namespace Dreamlabs\Tests\Library\Type;


use PHPUnit\Framework\TestCase;
use stdClass;
use Dreamlabs\GraphQL\Type\NonNullType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\GraphQL\Type\TypeService;

class NonNullTypeTest extends TestCase
{

    /**
     * @expectedException Dreamlabs\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidParams(): void
    {
        new NonNullType('invalid param');
    }

    public function testNonNullType(): void
    {
        $stringType      = new StringType();
        $nonNullType     = new NonNullType(new StringType());
        $nonNullOnString = new NonNullType(TypeMap::TYPE_STRING);
        $testArray       = ['a' => 'b'];

        $this->assertEquals($nonNullType->getName(), null, 'Empty non-null name');
        $this->assertEquals($nonNullType->getKind(), TypeMap::KIND_NON_NULL);
        $this->assertEquals($nonNullType->getType(), new NonNullType($stringType));
        $this->assertEquals($nonNullType->getNullableType(), $stringType);
        $this->assertEquals($nonNullType->getNullableType(), $nonNullOnString->getNullableType());
        $this->assertEquals($nonNullType->getNamedType(), $stringType);
        $this->assertEquals($nonNullType->getTypeOf(), $stringType);
        $this->assertEquals($nonNullType->isCompositeType(), true);
        $this->assertEquals(TypeService::isAbstractType($nonNullType), false);
        $this->assertFalse($nonNullType->isValidValue(null));
        $this->assertTrue($nonNullType->isValidValue($stringType));
        $this->assertFalse($nonNullType->isValidValue(new stdClass()));
        $this->assertEquals($nonNullType->parseValue($testArray), '');
        $this->assertEquals($nonNullType->resolve($testArray), $testArray);
    }

}
