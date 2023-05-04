<?php

namespace Dreamlabs\Tests\Library\Type;


use PHPUnit\Framework\TestCase;
use Dreamlabs\GraphQL\Field\Field;
use Dreamlabs\GraphQL\Type\InterfaceType\InterfaceType;
use Dreamlabs\GraphQL\Type\Object\ObjectType;
use Dreamlabs\GraphQL\Type\Scalar\StringType;
use Dreamlabs\Tests\DataProvider\TestExtendedType;
use Dreamlabs\Tests\DataProvider\TestInterfaceType;

class InterfaceTypeTest extends TestCase
{

    public function testInterfaceMethods(): void
    {
        $interface = new TestInterfaceType();
        $this->assertEquals($interface->getNamedType(), $interface->getType());
        $nameField = new Field(['name' => 'name', 'type' => new StringType()]);
        $nameField->getName();

        $this->assertEquals(['name' => $nameField],
            $interface->getFields());

        $object = new ObjectType([
            'name'       => 'Test',
            'fields'     => [
                'name' => new StringType()
            ],
            'interfaces' => [$interface],
        ]);
        $this->assertEquals([$interface], $object->getInterfaces());
        $this->assertTrue($interface->isValidValue($object));
        $this->assertFalse($interface->isValidValue('invalid object'));

        $this->assertEquals($interface->serialize($object), $object);

        $interfaceType = new InterfaceType([
            'name'        => 'UserInterface',
            'fields'      => [
                'name' => new StringType()
            ],
            'resolveType' => fn($object) => $object
        ]);
        $this->assertEquals('UserInterface', $interfaceType->getName());

        $this->assertEquals($object, $interfaceType->resolveType($object));

        $this->assertTrue($interfaceType->isValidValue($object));
        $this->assertFalse($interfaceType->isValidValue('invalid object'));
    }

    public function testApplyInterface(): void
    {
        $extendedType = new TestExtendedType();

        $this->assertArrayHasKey('ownField', $extendedType->getFields());
        $this->assertArrayHasKey('name', $extendedType->getFields());
    }

}
