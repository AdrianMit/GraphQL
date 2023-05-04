<?php

namespace Dreamlabs\Tests\Library\Utilities;

use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\GraphQL\Type\TypeMap;
use Dreamlabs\GraphQL\Type\TypeService;
use Dreamlabs\Tests\DataProvider\TestInterfaceType;
use Dreamlabs\Tests\DataProvider\TestObjectType;

class TypeUtilitiesTest extends TestCase
{

    public function testTypeService(): void
    {
        $this->assertTrue(TypeService::isScalarType(TypeMap::TYPE_STRING));
        $this->assertFalse(TypeService::isScalarType('gibberish'));
        $this->assertFalse(TypeService::isScalarType(new TestObjectType()));

        $stringType = new StringType();

        $this->assertFalse(TypeService::isInterface($stringType));
        $this->assertEquals(TypeService::resolveNamedType($stringType), $stringType);
        $this->assertNull(TypeService::resolveNamedType(null));
        $this->assertEquals(TypeService::resolveNamedType(123), $stringType);
    }

    /**
     * @expectedException \Exception
     */
    public function testNamedTypeResolverException(): void
    {
        TypeService::resolveNamedType(['name' => 'test']);
    }

    public function testIsInputType(): void
    {
        $testType = new ObjectType(['name' => 'test', 'fields' => ['name' => new StringType()]]);
        $this->assertTrue(TypeService::isInputType(new StringType()));
        $this->assertTrue(TypeService::isInputType(TypeMap::TYPE_STRING));
        $this->assertFalse(TypeService::isInputType('invalid type'));
        $this->assertFalse(TypeService::isInputType($testType));
    }

    public function testIsAbstractType(): void
    {
        $this->assertTrue(TypeService::isAbstractType(new TestInterfaceType()));
        $this->assertFalse(TypeService::isAbstractType(new StringType()));
        $this->assertFalse(TypeService::isAbstractType('invalid type'));
    }

    public function testGetPropertyValue(): void {
        $arrayData = (new TestObjectType())->getData();

        // Test with arrays
        $this->assertEquals('John', TypeService::getPropertyValue($arrayData, 'name'));
        $this->assertEquals('John', TypeService::getPropertyValue((object) $arrayData, 'name'));

        // Test with objects with getters
        $object = new ObjectWithVariousGetters();

        $this->assertEquals('John', TypeService::getPropertyValue($object, 'name'));
        $this->assertEquals('John Doe', TypeService::getPropertyValue($object, 'namedAfter'));
        $this->assertTrue(TypeService::getPropertyValue($object, 'true'));
        $this->assertFalse(TypeService::getPropertyValue($object, 'false'));
        $this->assertNull(TypeService::getPropertyValue($arrayData, 'doesntExist'));
        $this->assertNull(TypeService::getPropertyValue($object, 'doesntExist'));
    }
}

/**
 * Dummy class for testing getPropertyValue()
 */
class ObjectWithVariousGetters
{
    public function getName()
    {
        return 'John';
    }

    public function getNamedAfter()
    {
        return 'John Doe';
    }

    public function isTrue()
    {
        return true;
    }

    public function isFalse()
    {
        return false;
    }
}
